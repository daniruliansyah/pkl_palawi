<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\Jabatan;
use App\Models\User; // Dipertahankan karena dibutuhkan untuk notifikasi
use App\Notifications\StatusSuratDiperbarui; // Dipertahankan untuk notifikasi
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User; // Ditambahkan
use App\Notifications\StatusSuratDiperbarui; // Ditambahkan

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class SppdController extends Controller
{
    /**
     * Menampilkan daftar SPPD yang diajukan oleh user atau yang perlu diverifikasi oleh user.
     */
    public function index()
    {
        $user = Auth::user();
        $userNip = $user->nip;
        $userJabatanId = $user->jabatanTerbaru->id_jabatan ?? null;

        // Memuat SPPD di mana user adalah pemohon ATAU user adalah pemberi tugas (verifier)
        $sppds = Sppd::with('user')
            ->where('nip_user', $userNip)
            ->orWhere('pemberi_tugas_id', $userJabatanId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.surat_sppd.index', compact('sppds'));
    }

    /**
     * Menampilkan form untuk membuat SPPD baru.
     */
    public function create()
    {
        $jabatan = Jabatan::whereIn('nama_jabatan', [
            'General Manager',
            'Senior Analis Keuangan, SDM & Umum',
        ])->get();

        return view('pages.surat_sppd.create', compact('jabatan'));
    }

    /**
     * Menyimpan SPPD yang baru diajukan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pemberi_tugas_id' => 'required|exists:jabatan,id',
            'tgl_mulai'        => 'required|date',
            'tgl_selesai'      => 'required|date|after_or_equal:tgl_mulai',
            'keterangan_sppd'  => 'required|string',
            'lokasi_berangkat' => 'required|string|max:100',
            'lokasi_tujuan'    => 'required|string|max:100',
            'alat_angkat'      => 'required|string',
            'no_rekening'      => 'required|string',
            'nama_rekening'    => 'required|string',
            'keterangan_lain'  => 'nullable|string',
        ]);

        $tgl_mulai   = Carbon::parse($request->tgl_mulai);
        $tgl_selesai = Carbon::parse($request->tgl_selesai);
        // Hitung jumlah hari, termasuk hari mulai dan selesai (+1)
        $jumlah_hari = $tgl_mulai->diffInDays($tgl_selesai) + 1;

        $jabatan = Jabatan::find($request->pemberi_tugas_id);
        if (!$jabatan) {
            return back()->withInput()->withErrors(['pemberi_tugas_id' => 'Pemberi tugas tidak ditemukan.']);
        }

        // Simpan SPPD
        $sppd = Sppd::create([
            'nip_user'         => auth()->user()->nip,
            'pemberi_tugas'    => $jabatan->nama_jabatan,
            'pemberi_tugas_id' => $jabatan->id,
            'jumlah_hari'      => $jumlah_hari,
            'tgl_mulai'        => $request->tgl_mulai,
            'tgl_selesai'      => $request->tgl_selesai,
            'keterangan_sppd'  => $request->keterangan_sppd,
            'lokasi_berangkat' => $request->lokasi_berangkat,
            'lokasi_tujuan'    => $request->lokasi_tujuan,
            'alat_angkat'      => $request->alat_angkat,
            'no_rekening'      => $request->no_rekening,
            'nama_rekening'    => $request->nama_rekening,
            'keterangan_lain'  => $request->keterangan_lain,
            'status'           => 'menunggu',
        ]);

        //--- Logika Notifikasi Saat SPPD Diajukan ---
        try {
            // Cari user yang memiliki jabatan sebagai pemberi tugas
            $atasan = User::whereHas('jabatanTerbaru.jabatan', function ($query) use ($jabatan) {
                $query->where('id', $jabatan->id);
            })->first();

            if ($atasan) {
                $atasan->notify(new StatusSuratDiperbarui(
                    aktor: auth()->user(),
                    jenisSurat: 'SPPD',
                    statusBaru: 'Menunggu Persetujuan',
                    keterangan: 'Telah mengajukan SPPD baru dan membutuhkan persetujuan Anda.',
                    url: route('sppd.verifikasi', $sppd->id) // Sesuaikan dengan route yang relevan
                ));
            }
        } catch (\Exception $e) {
            Log::error("Notifikasi Gagal Dibuat (Store SPPD): " . $e->getMessage());
        }
        //--- Akhir Logika Notifikasi ---

        return redirect()->route('sppd.index')->with('success', 'SPPD berhasil diajukan!');
    }

    /**
     * Memperbarui status SPPD (Disetujui/Ditolak).
     */
    public function updateStatus(Request $request, Sppd $sppd)
    {
        // Cari user yang mengajukan SPPD untuk notifikasi
        $pembuatSppd = $sppd->user;
        $user = Auth::user();

        try {
            $request->validate([
                'status'           => 'required|in:Disetujui,Ditolak',
                'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak',
            ]);

            $userJabatanId = $user->jabatanTerbaru->jabatan->id ?? null;
            $pembuatSppd = $sppd->user; // Ambil user pembuat SPPD untuk notifikasi

            // Validasi kewenangan
            if ($userJabatanId !== $sppd->pemberi_tugas_id) {
                return back()->with('error', 'Anda tidak memiliki wewenang untuk melakukan aksi ini.');
            }
            
            if ($request->status === 'Disetujui') {
                $sppd->status           = 'Disetujui';
                $sppd->tgl_persetujuan  = now();
                $sppd->nip_penyetuju    = $user->nip;
                $sppd->no_surat         = $this->generateNoSurat();
                $sppd->alasan_penolakan = null;
                $sppd->save();

                $filePath = $this->generateSuratPdf($sppd);
                if ($filePath) {
                    $sppd->file_sppd = $filePath;
                    $sppd->save();

                    //--- Logika Notifikasi Saat SPPD Disetujui ---
                    if ($pembuatSppd) {
                        $pembuatSppd->notify(new StatusSuratDiperbarui(
                            aktor: $user,
                            jenisSurat: 'SPPD',
                            statusBaru: 'Disetujui',
                            keterangan: 'Surat SPPD Anda telah Disetujui dan siap diunduh.',
                            url: route('sppd.download', $sppd->id)
                        ));
                    }
                    //--- Akhir Logika Notifikasi ---

                } else {
                    return redirect()->route('sppd.index')->with('warning', 'Disetujui, tapi gagal membuat file surat. Cek logs.');
                }

                return redirect()->route('sppd.index')->with('success', 'Pengajuan SPPD berhasil disetujui dan surat telah dibuat.');
            
            } else { // Jika Ditolak
                $sppd->status           = 'Ditolak';
                $sppd->tgl_persetujuan  = now();
                $sppd->nip_penyetuju    = $user->nip;
                $sppd->alasan_penolakan = $request->alasan_penolakan;
                $sppd->save();

                //--- Logika Notifikasi Saat SPPD Ditolak ---
                if ($pembuatSppd) {
                    $alasan = $request->alasan_penolakan;
                    $pembuatSppd->notify(new StatusSuratDiperbarui(
                        aktor: $user,
                        jenisSurat: 'SPPD',
                        statusBaru: 'Ditolak',
                        keterangan: "Surat SPPD Anda Ditolak dengan alasan: {$alasan}",
                        url: route('sppd.index') // Arahkan ke halaman riwayat
                    ));
                }
                //--- Akhir Logika Notifikasi ---
                
                return redirect()->route('sppd.index')->with('success', 'Pengajuan SPPD berhasil ditolak.');
            }
        } catch (\Exception $e) {
            Log::error("Error in updateStatus: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Konversi angka ke format Romawi.
     */
    protected function numberToRoman($number)
    {
        $map = [
            'M'  => 1000, 'CM' => 900, 'D'  => 500, 'CD' => 400,
            'C'  => 100,  'XC' => 90,  'L'  => 50,  'XL' => 40,
            'X'  => 10,   'IX' => 9,   'V'  => 5,   'IV' => 4,
            'I'  => 1,
        ];

        $roman = '';
        while ($number > 0) {
            foreach ($map as $rom => $val) {
                if ($number >= $val) {
                    $number -= $val;
                    $roman  .= $rom;
                    break;
                }
            }
        }
        return $roman;
    }

    /**
     * Membuat nomor surat SPPD secara otomatis.
     */
    protected function generateNoSurat()
    {
        $year  = date('Y');
        $month = $this->numberToRoman(date('n'));

        $lastSppd = Sppd::whereYear('created_at', $year)
            ->whereNotNull('no_surat')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastSppd) {
            $parts = explode('/', $lastSppd->no_surat);
            // Ambil bagian nomor urut (bagian pertama)
            $lastNumber = isset($parts[0]) ? (int) $parts[0] : 0;
        }

        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return "{$newNumber}/WIL TIMUR/{$month}/{$year}";
    }

    /**
     * Membuat URL untuk verifikasi QR Code.
     */
    protected function generateQrCodeUrl(Sppd $sppd)
    {
        return route('sppd.verifikasi', ['id' => $sppd->id]);
    }

    /**
     * Membuat file PDF dari surat SPPD.
     */
    protected function generateSuratPdf(Sppd $sppd)
    {
        try {
            $fileName = "sppd_{$sppd->id}.pdf";
            $path     = storage_path("app/public/sppd/{$fileName}");

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $qrCodeUrl    = $this->generateQrCodeUrl($sppd);
            $options      = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 5,
                'eccLevel'    => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl);

            // 2. Load View dan Generate PDF
            $pdf = Pdf::loadView('pages.surat_sppd.test', compact('sppd', 'qrCodeBase64'))
                ->setOptions([
                    'isRemoteEnabled'      => true,
                    'isHtml5ParserEnabled' => true,
                ])
                ->setPaper('A4', 'portrait');

            // 3. Simpan PDF ke storage
            $pdf->save($path);
            return "sppd/{$fileName}";
        } catch (\Exception $e) {
            Log::error("PDF Generation Error [SPPD ID {$sppd->id}]: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Mengunduh file surat SPPD yang sudah jadi.
     */
    public function download($id)
    {
        $sppd = Sppd::findOrFail($id);

        if ($sppd->file_sppd && Storage::disk('public')->exists($sppd->file_sppd)) {
            $filePath = storage_path('app/public/' . $sppd->file_sppd);
            $safeName = str_replace('/', '-', $sppd->no_surat);
            return response()->download($filePath, "Surat-SPPD-{$safeName}.pdf");
        }

        return back()->with('error', 'File surat tidak ditemukan.');
    }

    /**
     * Menampilkan halaman verifikasi surat.
     */
    public function verifikasi($id)
    {
        $sppd = Sppd::with('user')->find($id);

        if (!$sppd) {
            // Sebaiknya buat view khusus untuk surat tidak ditemukan
            return response()->view('pages.surat_sppd.notfound', [], 404);
        }

        return view('pages.surat_sppd.info', compact('sppd'));
    }
}