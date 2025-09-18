<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SppdController extends Controller
{
    public function index()
    {
        $sppds = Sppd::with(['user.jabatanTerbaru.jabatan'])->get();
        return view('pages.surat_sppd.index', compact('sppds'));
    }

    public function create()
    {
        return view('pages.surat_sppd.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date|after_or_equal:tgl_mulai',
            'keterangan'    => 'required|string',
            'lokasi_tujuan' => 'required|string|max:100',
            'surat_bukti'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = [
            'nip_user'      => auth()->user()->nip,
            'tgl_mulai'     => $request->tgl_mulai,
            'tgl_selesai'   => $request->tgl_selesai,
            'keterangan'    => $request->keterangan,
            'lokasi_tujuan' => $request->lokasi_tujuan,
            'status_sdm'    => 'menunggu',
            'status_gm'     => 'menunggu',
        ];

        if ($request->hasFile('surat_bukti')) {
            $data['surat_bukti'] = $request->file('surat_bukti')->store('surat_bukti', 'public');
        }

        Sppd::create($data);

        return redirect()->route('sppd.index')
            ->with('success', 'Pengajuan SPPD berhasil dibuat, menunggu persetujuan.');
    }

    public function updateStatus(Request $request, Sppd $sppd)
    {
        $user = auth()->user();
        $userJabatan = $user->jabatanTerbaru->jabatan->nama_jabatan;

        $statusField = ($userJabatan == 'General Manager') ? 'status_gm' : 'status_sdm';
        $sppd->{$statusField} = $request->input('status');

        if ($request->input('status') == 'Disetujui') {
            if ($userJabatan == 'General Manager') {
                $sppd->nip_user_gm = $user->nip;
                $sppd->tgl_persetujuan_gm = now();
            } elseif ($userJabatan == 'Senior Analis Keuangan, SDM & Umum') {
                $sppd->nip_user_sdm = $user->nip;
                $sppd->tgl_persetujuan_sdm = now();
            }
        }

        if ($request->input('status') == 'Ditolak') {
            $sppd->reason = $request->input('reason');
        }

        $sppd->save();

        if ($sppd->status_sdm === 'Disetujui' && $sppd->status_gm === 'Disetujui') {
            // Generate nomor surat yang baru
            $sppd->no_surat = $this->generateNoSurat();
            $sppd->save(); // Penting: simpan nomor surat sebelum membuat PDF

            $filePath = $this->generateSuratPdf($sppd);

            if ($filePath) {
                $sppd->file_sppd = $filePath;
                $sppd->save();
            }

            return redirect()->route('sppd.index')
                ->with('success', 'Pengajuan SPPD berhasil disetujui, surat PDF telah dibuat!');
        }

        if ($request->input('status') == 'Disetujui') {
            return redirect()->route('sppd.index')
                ->with('success', 'Pengajuan SPPD berhasil disetujui!');
        } else {
            return redirect()->route('sppd.index')
                ->with('success', 'Pengajuan SPPD berhasil ditolak. Alasan telah dikirim.');
        }
    }

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

    protected function generateNoSurat()
    {
        $currentYear = date('Y');
        $currentRomanMonth = $this->numberToRoman(date('n'));

        // Cari SPPD terakhir yang dibuat di tahun dan bulan yang sama
        $lastSppd = Sppd::whereYear('created_at', $currentYear)
                        ->whereMonth('created_at', date('n'))
                        ->whereNotNull('no_surat')
                        ->orderBy('created_at', 'desc')
                        ->first();

        $lastNumber = 0;
        if ($lastSppd) {
            // Ambil nomor urut dari nomor surat terakhir
            $parts = explode('/', $lastSppd->no_surat);
            if (isset($parts[0])) {
                $lastNumber = (int) $parts[0];
            }
        }

        // Tambahkan 1 ke nomor urut terakhir dan format menjadi 4 digit
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return "{$newNumber}/WIL TIMUR/{$currentRomanMonth}/{$currentYear}";
    }

    protected function generateSuratPdf(Sppd $sppd)
    {
        try {
            $fileName = 'sppd_' . $sppd->id . '.pdf';
            $path = storage_path('app/public/sppd/' . $fileName);

            $pdf = Pdf::loadView('pages.surat_sppd.test', compact('sppd'));
            $pdf->save($path);

            return 'sppd/' . $fileName;

        } catch (\Exception $e) {
            Log::error("PDF Generation Error: " . $e->getMessage());
            return null;
        }
    }

    // SppdController.php

// ...

public function download($id)
{
    $sppd = Sppd::findOrFail($id);
    if ($sppd->file_sppd && Storage::disk('public')->exists($sppd->file_sppd)) {
        $filePath = storage_path('app/public/' . $sppd->file_sppd);

        // Ganti karakter "/" dengan "-" di nomor surat
        $safeFileName = str_replace('/', '-', $sppd->no_surat);

        // Mengunduh file dengan nama yang sudah diperbaiki
        return response()->download($filePath, 'Surat-SPPD-' . $safeFileName . '.pdf');
    }
    return redirect()->back()->with('error', 'File surat tidak ditemukan.');
}
}
