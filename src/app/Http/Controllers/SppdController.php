<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SppdController extends Controller
{
    public function index()
    {
        // Mendapatkan NIP user yang sedang login
        $userNip = Auth::user()->nip;
        // Mendapatkan ID jabatan user yang sedang login
        $userJabatanId = Auth::user()->jabatanTerbaru->id_jabatan ?? null;

        // Mendapatkan data SPPD yang dibuat oleh user yang login (untuk karyawan biasa)
        // ATAU yang ditujukan kepada jabatan user yang login (untuk SDM/GM)
        $sppds = Sppd::with('user')
            ->where('nip_user', $userNip)
            ->orWhere('pemberi_tugas_id', $userJabatanId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mengirimkan data SPPD ke view
        return view('pages.surat_sppd.index', compact('sppds'));
    }

    public function create()
    {
        // Mengambil data jabatan yang boleh menjadi pemberi tugas
        $jabatan = Jabatan::whereIn('nama_jabatan', [
            'General Manager',
            'Senior Analis Keuangan, SDM & Umum'
        ])->get();

        return view('pages.surat_sppd.create', compact('jabatan'));
    }

    public function store(Request $request)
    {
        // Validasi data input dari form
        $request->validate([
            'pemberi_tugas_id'   => 'required|exists:jabatan,id',
            'tgl_mulai'          => 'required|date',
            'tgl_selesai'        => 'required|date|after_or_equal:tgl_mulai',
            'keterangan_sppd'    => 'required|string',
            'lokasi_berangkat'   => 'required|string|max:100',
            'lokasi_tujuan'      => 'required|string|max:100',
            'alat_angkat'        => 'required|string',
            'no_rekening'        => 'required|string',
            'nama_rekening'      => 'required|string',
            'keterangan_lain'    => 'nullable|string',
            // Aturan validasi alasan penolakan TIDAK ADA DI SINI.
            // Aturan ini hanya untuk update status.
        ]);

        $tgl_mulai = Carbon::parse($request->tgl_mulai);
        $tgl_selesai = Carbon::parse($request->tgl_selesai);
        $jumlah_hari = $tgl_mulai->diffInDays($tgl_selesai) + 1;

        $pemberiTugasId = $request->pemberi_tugas_id;
        $jabatan = Jabatan::find($pemberiTugasId);

        if (!$jabatan) {
            return redirect()->back()->withInput()->withErrors(['pemberi_tugas_id' => 'Pemberi tugas tidak ditemukan.']);
        }

        Sppd::create([
            'nip_user'          => auth()->user()->nip,
            'pemberi_tugas'     => $jabatan->nama_jabatan,
            'pemberi_tugas_id'  => $jabatan->id,
            'jumlah_hari'       => $jumlah_hari,
            'tgl_mulai'         => $request->tgl_mulai,
            'tgl_selesai'       => $request->tgl_selesai,
            'keterangan_sppd'   => $request->keterangan_sppd,
            'lokasi_berangkat'  => $request->lokasi_berangkat,
            'lokasi_tujuan'     => $request->lokasi_tujuan,
            'alat_angkat'       => $request->alat_angkat,
            'no_rekening'       => $request->no_rekening,
            'nama_rekening'     => $request->nama_rekening,
            'keterangan_lain'   => $request->keterangan_lain,
            'status'            => 'menunggu',
        ]);

        return redirect()->route('sppd.index')->with('success', 'SPPD berhasil diajukan!');
    }

    public function updateStatus(Request $request, Sppd $sppd)
    {
        try {
            // Menambahkan validasi untuk 'alasan_penolakan'
            $request->validate([
                'status' => 'required|in:Disetujui,Ditolak',
                'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak',
            ]);

            $user = Auth::user();
            $userJabatanId = $user->jabatanTerbaru->jabatan->id ?? null;

            if ($userJabatanId !== $sppd->pemberi_tugas_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk melakukan aksi ini.');
            }

            $newStatus = $request->input('status');

            if ($newStatus === 'Disetujui') {
                $sppd->status = 'Disetujui';
                $sppd->tgl_persetujuan = now();
                $sppd->nip_penyetuju = $user->nip;
                $sppd->no_surat = $this->generateNoSurat();
                // Mengosongkan alasan penolakan jika disetujui
                $sppd->alasan_penolakan = null;
                $sppd->save();

                $filePath = $this->generateSuratPdf($sppd);
                if ($filePath) {
                    $sppd->file_sppd = $filePath;
                    $sppd->save();
                } else {
                    return redirect()->route('sppd.index')->with('warning', 'Pengajuan SPPD berhasil disetujui, namun gagal membuat file surat. Silakan coba lagi.');
                }
                return redirect()->route('sppd.index')->with('success', 'Pengajuan SPPD berhasil disetujui dan surat telah dibuat.');
            } else { // 'Ditolak'
                // Menambahkan validasi alasan hanya jika statusnya Ditolak
                // if (empty($request->input('alasan_penolakan'))) {
                //     return redirect()->back()->with('error', 'Alasan penolakan tidak boleh kosong.');
                // }
                // Validasi di atas sudah digantikan oleh required_if

                $sppd->status = 'Ditolak';
                $sppd->tgl_persetujuan = now();
                $sppd->nip_penyetuju = $user->nip;
                $sppd->alasan_penolakan = $request->input('alasan_penolakan'); // Menyimpan alasan penolakan
                $sppd->save();
                return redirect()->route('sppd.index')->with('success', 'Pengajuan SPPD berhasil ditolak.');
            }
        } catch (\Exception $e) {
            Log::error("Error in updateStatus: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Fungsi helper untuk mengubah angka menjadi Romawi
    protected function numberToRoman($number)
    {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $roman = '';
        while ($number > 0) {
            foreach ($map as $rom => $val) {
                if ($number >= $val) {
                    $number -= $val;
                    $roman .= $rom;
                    break;
                }
            }
        }
        return $roman;
    }

    // Fungsi helper untuk membuat nomor surat baru
    protected function generateNoSurat()
    {
        $currentYear = date('Y');
        $currentRomanMonth = $this->numberToRoman(date('n'));

        $lastSppd = Sppd::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', date('n'))
            ->whereNotNull('no_surat')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastSppd) {
            $parts = explode('/', $lastSppd->no_surat);
            if (isset($parts[0])) {
                $lastNumber = (int) $parts[0];
            }
        }
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return "{$newNumber}/WIL TIMUR/{$currentRomanMonth}/{$currentYear}";
    }

    // Fungsi helper untuk membuat file PDF surat
    protected function generateSuratPdf(Sppd $sppd)
    {
        try {
            $fileName = 'sppd_' . $sppd->id . '.pdf';
            $path = storage_path('app/public/sppd/' . $fileName);

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $pdf = Pdf::loadView('pages.surat_sppd.test', compact('sppd'))
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                ])
                ->setPaper('A4', 'portrait');

            $pdf->save($path);
            return 'sppd/' . $fileName;
        } catch (\Exception $e) {
            Log::error("PDF Generation Error: " . $e->getMessage());
            return null;
        }
    }

    // Fungsi untuk mengunduh file PDF surat
    public function download($id)
    {
        $sppd = Sppd::findOrFail($id);
        if ($sppd->file_sppd && Storage::disk('public')->exists($sppd->file_sppd)) {
            $filePath = storage_path('app/public/' . $sppd->file_sppd);
            $safeFileName = str_replace('/', '-', $sppd->no_surat);
            return response()->download($filePath, 'Surat-SPPD-' . $safeFileName . '.pdf');
        }
        return redirect()->back()->with('error', 'File surat tidak ditemukan.');
    }
}
