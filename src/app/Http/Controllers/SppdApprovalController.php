<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sppd;
use App\Models\User;
use ZipArchive;
use App\Notifications\StatusSuratDiperbarui; // <-- PENTING: Class Notifikasi Diimpor
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <-- PENTING: Diperlukan untuk error logging
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use chillerlan\QRCode\QRCode; // Diperlukan untuk generateSuratPdf
use chillerlan\QRCode\QROptions; // Diperlukan untuk generateSuratPdf

// --- TAMBAHKAN DUA BARIS INI ---
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class SppdApprovalController extends Controller
{
    /**
     * Menampilkan halaman persetujuan SPPD.
     * Hanya bisa diakses oleh approver (SDM dan GM).
     */
    public function index()
    {
        $user = Auth::user();

        // Otorisasi: hanya SDM dan GM yang bisa mengakses
        // Asumsi method isSdm() dan isGm() ada di Model User
        if (!$user->isSdm() && !$user->isGm()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman persetujuan SPPD.');
        }

        // Ambil data SPPD yang menunggu persetujuan dari user ini
        $jabatanId = $user->jabatanTerbaru->id_jabatan ?? null;

        $sppdsForApproval = Sppd::where('pemberi_tugas_id', $jabatanId)
                                 ->where('status', 'menunggu')
                                 ->with('user.jabatanTerbaru.jabatan', 'penyetuju')
                                 ->latest()
                                 ->get();

        // Ambil riwayat SPPD yang pernah diproses oleh user ini
        $sppdsHistory = Sppd::where('nip_penyetuju', $user->nip)
                            ->with('user.jabatanTerbaru.jabatan', 'penyetuju')
                            ->latest()
                            ->get();

        return view('pages.surat_sppd.approval', compact('sppdsForApproval', 'sppdsHistory'));
    }

    /**
     * Mengupdate status pengajuan SPPD (menyetujui atau menolak).
     */
    public function update(Request $request, Sppd $sppd)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|required_if:status,Ditolak',
        ]);

        $user = Auth::user();
        $pembuatSppd = $sppd->user; // Mendapatkan objek User yang mengajukan SPPD

        if ($sppd->pemberi_tugas_id !== ($user->jabatanTerbaru->id_jabatan ?? null)) {
            return redirect()->back()->with('error', 'Anda tidak berwenang untuk memproses pengajuan ini.');
        }

        // Simpan status dan informasi penyetuju sementara
        $sppd->status = $request->status;
        $sppd->nip_penyetuju = $user->nip;
        $sppd->tgl_persetujuan = now();

        try {
            if ($request->status === 'Disetujui') {
                $sppd->alasan_penolakan = null;
                $sppd->no_surat = $this->generateNoSurat();
                $sppd->save(); // Simpan perubahan status dan nomor surat

                $pdfGenerated = $this->generateSuratPdf($sppd);

                if (!$pdfGenerated) {
                    return redirect()->route('sppd.approvals.index')->with('warning', 'SPPD disetujui, tapi file PDF gagal dibuat. Silakan cek logs.');
                }

                // --- LOGIKA NOTIFIKASI SAAT DISETUJUI ---
                if ($pembuatSppd) {
                    $pembuatSppd->notify(new StatusSuratDiperbarui(
                        aktor: $user, // AKTOR: User yang menyetujui (Approver)
                        jenisSurat: 'SPPD',
                        statusBaru: 'Disetujui',
                        keterangan: 'Surat SPPD Anda telah Disetujui dan siap diunduh.',
                        url: route('sppd.download', $sppd->id) // Arahkan ke link download
                    ));
                }
                // --- AKHIR LOGIKA NOTIFIKASI ---

            } else { // Jika status Ditolak
                $sppd->alasan_penolakan = $request->alasan_penolakan;
                $sppd->save(); // Simpan perubahan status penolakan

                // --- LOGIKA NOTIFIKASI SAAT DITOLAK ---
                if ($pembuatSppd) {
                    $alasan = $request->alasan_penolakan;
                    $pembuatSppd->notify(new StatusSuratDiperbarui(
                        aktor: $user, // AKTOR: User yang menolak (Approver)
                        jenisSurat: 'SPPD',
                        statusBaru: 'Ditolak',
                        keterangan: "Surat SPPD Anda Ditolak dengan alasan: {$alasan}",
                        url: route('sppd.index') // Arahkan ke halaman riwayat
                    ));
                }
                // --- AKHIR LOGIKA NOTIFIKASI ---
            }

            return redirect()->route('sppd.approvals.index')->with('success', 'Status SPPD berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error("Error saat memperbarui status SPPD: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses permintaan.');
        }
    }

  // ... (Setelah fungsi update() dan sebelum numberToRoman())

    /**
     * FUNGSI BARU: Membuat laporan arsip ZIP dari SPPD yang sudah disetujui.
     */
    public function downloadReport(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string', // 'all' atau angka 1-12
            'tahun' => 'required|integer|min:2020|max:' . date('Y'),
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Query ke model Sppd
        $query = Sppd::where('status', 'Disetujui') // Status disetujui
                    ->whereNotNull('file_sppd')     // Pastikan file PDF-nya ada
                    ->whereYear('tgl_berangkat', $tahun); // Gunakan tgl_berangkat

        if ($bulan !== 'all') {
            $query->whereMonth('tgl_berangkat', $bulan);
        }

        $suratSPPD = $query->with('user')->get(); // Ambil data SPPD

        if ($suratSPPD->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada SPPD yang ditemukan untuk periode yang dipilih.');
        }

        $namaBulan = ($bulan !== 'all') ? Carbon::create()->month($bulan)->isoFormat('MMMM') : 'Setahun';
        $zipFileName = 'laporan-sppd-' . $namaBulan . '-' . $tahun . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($suratSPPD as $surat) {
                // $surat->file_sppd adalah path di 'public' disk, misal: 'sppd/nama-file.pdf'
                $filePath = storage_path('app/public/' . $surat->file_sppd);

                if (File::exists($filePath)) {
                    // Buat nama file yang deskriptif di dalam ZIP
                    $newFileName = 'SPPD-' . str_replace(' ', '_', $surat->user->nama_lengkap) . '-' . $surat->tgl_berangkat . '.pdf';
                    $zip->addFile($filePath, $newFileName);
                }
            }
            $zip->close();
        } else {
            return redirect()->back()->with('error', 'Gagal membuat file arsip ZIP.');
        }

        // Download file ZIP lalu hapus setelah terkirim
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }


    // =================================================================
    //  METHOD HELPER YANG DISALIN DARI SppdController (Tetap diperlukan)
    // =================================================================

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
        // Asumsi route 'sppd.verifikasi' ada
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

            // QR Code Generation (menggunakan full namespace karena tidak di-import di atas)
            $qrCodeUrl     = $this->generateQrCodeUrl($sppd);
            $options       = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 5,
                'eccLevel'    => QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new QRCode($options))->render($qrCodeUrl);

            // Load View dan Generate PDF
            $pdf = Pdf::loadView('pages.surat_sppd.test', compact('sppd', 'qrCodeBase64'))
                ->setOptions([
                    'isRemoteEnabled'      => true,
                    'isHtml5ParserEnabled' => true,
                ])
                ->setPaper('A4', 'portrait');

            // Simpan PDF ke storage
            $pdf->save($path);

            // Simpan path file ke database (PENTING: Agar file_sppd terisi)
            $sppd->file_sppd = "sppd/{$fileName}";
            $sppd->save();

            return true;
        } catch (\Exception $e) {
            Log::error("PDF Generation Error [SPPD ID {$sppd->id}]: " . $e->getMessage());
            return false;
        }
    }
}
