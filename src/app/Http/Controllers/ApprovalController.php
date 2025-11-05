<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User; // Pastikan User di-import
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;
// Import CutiController untuk menggunakan logikanya
use App\Http\Controllers\CutiController;

class ApprovalController extends Controller
{
    /**
     * === FUNGSI INDEX DIPERBARUI ===
     * Menggunakan logika yang sama persis dengan CutiController@indexApproval
     * untuk memastikan semua peran (termasuk Manager) bisa melihat antrian mereka.
     */
    public function index()
    {
        $user = Auth::user();
        $userNip = $user->nip; // Ambil NIP sekali saja

        // 1. General Manager (GM)
        if ($user->isGm()) {
            $cutisForApproval = Cuti::where(function ($query) {
                // Alur 1: Karyawan Biasa
                // PERBAIKAN: Mengganti ->isKaryawanBiasa() dengan query jabatan
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => 
                        $j->where('nama_jabatan', 'NOT LIKE', '%General Manager%')
                          ->where('nama_jabatan', 'NOT LIKE', '%Manager%')
                          ->where('nama_jabatan', 'NOT LIKE', '%Senior%')
                      )
                    ->where('status_sdm', 'Disetujui')
                    ->where('status_gm', 'Menunggu Persetujuan');
            })->orWhere(function ($query) {
                // Alur 2: Senior
                // PERBAIKAN: Mengganti ->isSenior() dengan query jabatan
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => 
                        $j->where('nama_jabatan', 'LIKE', '%Senior%')
                          ->where('nama_jabatan', 'NOT LIKE', '%Senior Analis Keuangan, SDM & Umum%')
                      )
                    ->where('status_sdm', 'Disetujui')
                    ->where('status_gm', 'Menunggu Persetujuan');
            })->orWhere(function ($query) {
                // Alur 3: SDM
                // PERBAIKAN: Mengganti ->isSdm() dengan query jabatan
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => $j->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))
                    ->where('status_manager', 'Disetujui')
                    ->where('status_gm', 'Menunggu Persetujuan');
            })->orWhere(function ($query) {
                // Alur 4: Manager
                // PERBAIKAN: Mengganti ->isManager() dengan query jabatan
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => 
                        $j->where('nama_jabatan', 'LIKE', '%Manager%')
                          ->where('nama_jabatan', 'NOT LIKE', '%General Manager%')
                      )
                    ->where('status_gm', 'Menunggu Persetujuan');
            })->latest()->get();
            
            // Mengambil logika history dari kode lama Anda
            $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')->where(function ($query) { $query->where('status_gm', 'Disetujui')->orWhere('status_ssdm', 'Ditolak')->orWhere('status_sdm', 'Ditolak')->orWhere('status_gm', 'Ditolak'); })->latest()->get();
            return view('pages.approval.index-gm', compact('cutisForApproval', 'cutisHistory'));
        }

        // 2. Manager
    if ($user->isManager()) {
        $cutisForApproval = Cuti::where(function ($query) use ($userNip) {
            // Alur 3: Menunggu persetujuan Manager dari SDM
            $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => $j->where('nama_jabatan', 'LIKE', '%Senior Analis Keuangan, SDM & Umum%'))
                ->where('status_manager', 'Menunggu Persetujuan')
                ->where('nip_user_manager', $userNip);
        })
        // === PERBAIKAN: Mengaktifkan Alur 1 (Manager sebagai SSDM) ===
        ->orWhere(function ($query) use ($userNip) {
            // Alur 1 (Jika Manager juga bertindak sebagai SSDM/Atasan Langsung)
            $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => 
                    $j->where('nama_jabatan', 'NOT LIKE', '%General Manager%')
                        ->where('nama_jabatan', 'NOT LIKE', '%Manager%')
                        ->where('nama_jabatan', 'NOT LIKE', '%Senior%')
                )
                ->where('status_ssdm', 'Menunggu Persetujuan')
                ->where('nip_user_ssdm', $userNip);
        })
        // === SELESAI PERBAIKAN ===
        ->latest()->get();

        // === PERBAIKAN: Memperbaiki query Riwayat Tindakan Manager ===
        $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')
            ->where(function($query) use ($userNip) {
                // Dia adalah approver SSDM (Alur 1) DAN dia sudah bertindak
                $query->where('nip_user_ssdm', $userNip)
                    ->where('status_ssdm', '!=', 'Menunggu Persetujuan');
            })->orWhere(function($query) use ($userNip) {
                // Dia adalah approver Manager (Alur 3) DAN dia sudah bertindak
                $query->where('nip_user_manager', $userNip)
                    ->where('status_manager', '!=', 'Menunggu Persetujuan')
                    ->where('status_manager', '!=', 'Menunggu');
            })
            ->latest()
            ->get();

        return view('pages.approval.index-manager', compact('cutisForApproval', 'cutisHistory'));
    }

        // 3. SDM (Senior Analis Keuangan, SDM & Umum)
    if ($user->isSdm()) {
        $cutisForApproval = Cuti::where(function ($query) {
            // Alur 1: Karyawan Biasa -> SSDM -> SDM
            $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => 
                    $j->where('nama_jabatan', 'NOT LIKE', '%General Manager%')
                        ->where('nama_jabatan', 'NOT LIKE', '%Manager%')
                        ->where('nama_jabatan', 'NOT LIKE', '%Senior%')
                )
                ->where('status_ssdm', 'Disetujui')
                ->where('status_sdm', 'Menunggu Persetujui');
        })->orWhere(function ($query) {
            // Alur 2: Senior -> SDM
            $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => 
                    $j->where('nama_jabatan', 'LIKE', '%Senior%')
                        ->where('nama_jabatan', 'NOT LIKE', '%Senior Analis Keuangan, SDM & Umum%')
                )
                ->where('status_ssdm', 'Disetujui') // Senior sdh approve (by pass)
                ->where('status_sdm', 'Menunggu Persetujuan');
        })->orWhere(function ($query) {
            // === PERBAIKAN: Mengganti Alur 5 (GM) dengan Alur 4 (Manager) ===
            // Alur 4: Manager -> SDM
            $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => 
                    $j->where('nama_jabatan', 'LIKE', '%Manager%')
                        ->where('nama_jabatan', 'NOT LIKE', '%General Manager%')
                )
                ->where('status_ssdm', 'Disetujui') // Manager bypass
                ->where('status_manager', 'Disetujui') // Manager bypass
                ->where('status_sdm', 'Menunggu Persetujuan');
            // === SELESAI PERBAIKAN ===
        })->latest()->get();

        // Mengambil logika history dari kode lama Anda
        $cutisHistory = Cuti::with('user.jabatanTerbaru.jabatan')->where(function ($query) { $query->where('status_gm', 'Disetujui')->orWhere('status_ssdm', 'Ditolak')->orWhere('status_sdm', 'Ditolak')->orWhere('status_gm', 'Ditolak'); })->latest()->get();
        return view('pages.approval.index-sdm', compact('cutisForApproval', 'cutisHistory'));
    }

        // 4. Senior (SSDM) - Atasan Langsung
        if ($user->isSenior()) { // HANYA isSenior(), karena isManager() sudah ditangani di atas
            $cutisForApproval = Cuti::where(function ($query) use ($userNip) {
                // Alur 1: Karyawan Biasa -> SSDM
                // PERBAIKAN: Mengganti ->isKaryawanBiasa() dengan query jabatan
                $query->whereHas('user.jabatanTerbaru.jabatan', fn ($j) => 
                        $j->where('nama_jabatan', 'NOT LIKE', '%General Manager%')
                          ->where('nama_jabatan', 'NOT LIKE', '%Manager%')
                          ->where('nama_jabatan', 'NOT LIKE', '%Senior%')
                      )
                    ->where('status_ssdm', 'Menunggu Persetujuan')
                    ->where('nip_user_ssdm', $userNip);
            })->latest()->get();
            
            // Mengambil logika history dari kode lama Anda
            $cutisHistory = Cuti::with('user')->where('status_ssdm', '!=', 'Menunggu Persetujuan')->where('nip_user_ssdm', $userNip)->latest()->get();
            return view('pages.approval.index-ssdm', compact('cutisForApproval', 'cutisHistory'));
        }

        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman persetujuan.');
    }


    /**
     * Mengupdate status pengajuan cuti.
     * Menggunakan logika dari CutiController@updateStatus untuk menjaga konsistensi alur approval.
     */
    public function update(Request $request, Cuti $cuti)
    {
        // PENTING: Untuk menghindari duplikasi logika dan menjaga satu sumber kebenaran (single source of truth),
        // Sebaiknya action update di ApprovalController ini diarahkan untuk menggunakan logic di CutiController@updateStatus.
        // Karena kodenya sudah dipisahkan, saya akan memanggil CutiController@updateStatus.
        // Anda HARUS memastikan rute 'approvals.update' memanggil method ini.

        $cutiController = new CutiController();
        return $cutiController->updateStatus($request, $cuti);
    }

    // ... method downloadReport() tidak diubah ...
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

