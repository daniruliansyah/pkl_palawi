<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sppd; // Pastikan model SPPD di-import
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

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
        if (!$user->isSdm() && !$user->isGm()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman persetujuan SPPD.');
        }

        // Ambil data SPPD yang menunggu persetujuan dari user ini
        // Asumsi pemberi tugas ditentukan oleh ID jabatan
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

        if ($sppd->pemberi_tugas_id !== ($user->jabatanTerbaru->id_jabatan ?? null)) {
            return redirect()->back()->with('error', 'Anda tidak berwenang untuk memproses pengajuan ini.');
        }

        $sppd->status = $request->status;
        $sppd->nip_penyetuju = $user->nip;
        $sppd->tgl_persetujuan = now();

        if ($request->status === 'Disetujui') {
            // --- TAMBAHAN PENTING ---
            $sppd->alasan_penolakan = null;
            $sppd->no_surat = $this->generateNoSurat();
            // ------------------------
            $sppd->save(); // Simpan dulu no_surat
            
            // --- PERBAIKAN PEMANGGILAN PDF ---
            $pdfGenerated = $this->generateSuratPdf($sppd); // Panggil method yang sudah disalin
            
            if (!$pdfGenerated) {
                return redirect()->route('sppd.approvals.index')->with('warning', 'SPPD disetujui, tapi file PDF gagal dibuat. Silakan cek logs.');
            }
            
        } else { // Jika status Ditolak
            $sppd->alasan_penolakan = $request->alasan_penolakan;
            $sppd->save();
        }

        return redirect()->route('sppd.approvals.index')->with('success', 'Status SPPD berhasil diperbarui.');
    }

    // =================================================================
    //  METHOD HELPER YANG DISALIN DARI SppdController
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
            $options      = new \chillerlan\QRCode\QROptions([ // Pastikan namespace-nya benar
                'outputType'  => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'scale'       => 5,
                'eccLevel'    => \chillerlan\QRCode\QRCode::ECC_H,
            ]);
            $qrCodeBase64 = (new \chillerlan\QRCode\QRCode($options))->render($qrCodeUrl);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.surat_sppd.test', compact('sppd', 'qrCodeBase64'))
                ->setOptions([
                    'isRemoteEnabled'      => true,
                    'isHtml5ParserEnabled' => true,
                ])
                ->setPaper('A4', 'portrait');

            $pdf->save($path);
            
            // Simpan path file ke database
            $sppd->file_sppd = "sppd/{$fileName}";
            $sppd->save();

            return true; // Mengembalikan status sukses
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("PDF Generation Error [SPPD ID {$sppd->id}]: " . $e->getMessage());
            return false; // Mengembalikan status gagal
        }
    }
}

