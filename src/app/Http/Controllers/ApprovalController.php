<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;
use App\Http\Controllers\CutiController;

class ApprovalController extends Controller
{
    /**
     * === LOGIKA HYBRID (PERAN STRUKTURAL + ATASAN LANGSUNG) ===
     * Memastikan User melihat tugas utamanya (misal: GM/Manager)
     * TAPI juga melihat tugas tambahannya sebagai Atasan Langsung (SSDM) jika punya anak buah.
     */
    public function index()
    {
        $user = Auth::user();
        $userNip = $user->nip;

        // 1. General Manager (GM)
        if ($user->isGm()) {
            $cutisForApproval = Cuti::where(function ($query) use ($userNip) {
                // TUGAS UTAMA: Sebagai GM (Final Approval)
                $query->where('status_gm', 'Menunggu Persetujuan')
                      ->where(function($q) {
                          $q->where('status_sdm', 'Disetujui')
                            ->orWhere('status_manager', 'Disetujui');
                      });
            })->orWhere(function ($query) use ($userNip) {
                // TUGAS TAMBAHAN: Jika GM juga dipilih sebagai Atasan Langsung (SSDM)
                // Walaupun jarang, sistem harus mengakomodasi ini.
                $query->where('nip_user_ssdm', $userNip)
                      ->where('status_ssdm', 'Menunggu Persetujuan');
            })->latest()->get();

            // History GM
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where('nip_user_gm', $userNip)
                ->where('status_gm', '!=', 'Menunggu Persetujuan')
                ->where('status_gm', '!=', 'Menunggu')
                ->latest()->get();

            return view('pages.approval.index-gm', compact('cutisForApproval', 'cutisHistory'));
        }

        // 2. Manager
        if ($user->isManager()) {
            $cutisForApproval = Cuti::where(function ($query) use ($userNip) {
                // TUGAS UTAMA: Sebagai Manager (Cek pengajuan orang SDM) - Alur 3
                $query->where('nip_user_manager', $userNip)
                      ->where('status_manager', 'Menunggu Persetujuan')
                      // Filter jabatan ini opsional, tapi bagus untuk memastikan hanya SDM yang masuk sini
                      ->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => 
                          $j->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%')
                      );
            })
            ->orWhere(function ($query) use ($userNip) {
                // TUGAS TAMBAHAN: Sebagai Atasan Langsung/SSDM (Alur 1 & 4)
                // Ini menangani approval untuk staf biasa di bawah manager tersebut
                $query->where('nip_user_ssdm', $userNip)
                      ->where('status_ssdm', 'Menunggu Persetujuan');
            })
            ->latest()->get();

            // History Manager (Gabungan history struktural & history atasan langsung)
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where(function($q) use ($userNip) {
                    $q->where('nip_user_manager', $userNip)
                      ->where('status_manager', '!=', 'Menunggu Persetujuan')
                      ->where('status_manager', '!=', 'Menunggu');
                })
                ->orWhere(function($q) use ($userNip) {
                    $q->where('nip_user_ssdm', $userNip)
                      ->where('status_ssdm', '!=', 'Menunggu Persetujuan');
                })
                ->latest()->get();

            return view('pages.approval.index-manager', compact('cutisForApproval', 'cutisHistory'));
        }

        // 3. SDM (Senior Analis Keuangan, SDM & Umum)
        if ($user->isSdm()) {
            $cutisForApproval = Cuti::where(function ($query) {
                // TUGAS UTAMA: Sebagai Verifikator HR (Menerima dari SSDM/Manager)
                $query->where('status_sdm', 'Menunggu Persetujuan')
                      ->where(function($subQ) {
                          $subQ->where('status_ssdm', 'Disetujui') // Dari Alur 1 & 2
                               ->orWhere('status_manager', 'Disetujui'); // Dari Alur 4
                      });
            })
            ->orWhere(function ($query) use ($userNip) {
                // TUGAS TAMBAHAN: Sebagai Atasan Langsung/SSDM (PENTING!)
                // Jika Staff SDM mengajukan cuti, Senior SDM ini bertindak sebagai SSDM-nya dulu
                $query->where('nip_user_ssdm', $userNip)
                      ->where('status_ssdm', 'Menunggu Persetujuan');
            })
            ->latest()->get();

            // History SDM (Gabungan)
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')
                ->where(function($q) use ($userNip) {
                    // History sebagai HR
                    $q->where('nip_user_sdm', $userNip)
                      ->where('status_sdm', '!=', 'Menunggu Persetujuan')
                      ->where('status_sdm', '!=', 'Menunggu');
                })
                ->orWhere(function($q) use ($userNip) {
                    // History sebagai Atasan Langsung
                    $q->where('nip_user_ssdm', $userNip)
                      ->where('status_ssdm', '!=', 'Menunggu Persetujuan');
                })
                ->latest()->get();

            return view('pages.approval.index-sdm', compact('cutisForApproval', 'cutisHistory'));
        }

        // 4. Senior (SSDM / Atasan Langsung)
        // Blok ini menangkap User yang HANYA Senior (Bukan GM, Bukan Manager, Bukan SDM)
        if ($user->isSenior()) { 
            $cutisForApproval = Cuti::where(function ($query) use ($userNip) {
                // KUNCI PERBAIKAN: Percaya pada NIP yang tersimpan di kolom nip_user_ssdm
                // Jangan filter jabatan user pemohon lagi di sini, itu bikin bug.
                $query->where('nip_user_ssdm', $userNip)
                      ->where('status_ssdm', 'Menunggu Persetujuan');
            })->latest()->get();
           
            $cutisHistory = Cuti::with('user')
                ->where('nip_user_ssdm', $userNip)
                ->where('status_ssdm', '!=', 'Menunggu Persetujuan')
                ->latest()->get();

            return view('pages.approval.index-ssdm', compact('cutisForApproval', 'cutisHistory'));
        }

        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman persetujuan.');
    }

    /**
     * Update memanggil CutiController agar logic status terpusat
     */
    public function update(Request $request, Cuti $cuti)
    {
        $cutiController = new CutiController();
        return $cutiController->updateStatus($request, $cuti);
    }

    // Method downloadReport biarkan seperti kode asli Anda
    public function downloadReport(Request $request)
    {
        $request->validate([ 'bulan' => 'required|string', 'tahun' => 'required|integer|min:2020|max:' . date('Y'), ]);
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $query = Cuti::where('status_gm', 'Disetujui')->whereNotNull('file_surat')->whereYear('tgl_mulai', $tahun);
        if ($bulan !== 'all') { $query->whereMonth('tgl_mulai', $bulan); }
        $suratCuti = $query->get();

        if ($suratCuti->isEmpty()) { return redirect()->back()->with('error', 'Tidak ada surat cuti yang ditemukan untuk periode yang dipilih.'); }

        $namaBulan = ($bulan !== 'all') ? \Carbon\Carbon::create()->month($bulan)->isoFormat('MMMM') : 'Setahun';
        $zipFileName = 'laporan-cuti-' . $namaBulan . '-' . $tahun . '.zip';
        $zipPath = storage_path('app/public/'.".$zipFileName");
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($suratCuti as $surat) {
                $filePath = storage_path('app/public/' . $surat->file_surat);
                if (File::exists($filePath)) {
                    $newFileName = 'cuti-' . str_replace(' ', '_', $surat->user->nama_lengkap) . '-' . $surat->tgl_mulai . '.pdf';
                    $zip->addFile($filePath, $newFileName);
                }
            }
            $zip->close();
        } else { return redirect()->back()->with('error', 'Gagal membuat file arsip ZIP.'); }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}