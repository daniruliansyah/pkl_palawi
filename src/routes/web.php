<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SppdController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\RiwayatJabatanController;
use App\Http\Controllers\SPController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\SppdApprovalController; // Tambahkan ini

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('layouts.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rute verifikasi publik
Route::get('/sppd/verifikasi/{id}', [SppdController::class, 'verifikasi'])->name('sppd.verifikasi');
Route::get('/sp/verifikasi/{id}', [SPController::class, 'verifikasi'])->name('sp.verifikasi');

Route::middleware('auth')->group(function () {
    // Rute Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/detail', [ProfileController::class, 'show'])->name('profile.show');

    // Rute Karyawan dan Riwayat Jabatan
    Route::resource('karyawan', UserController::class)->middleware('check.karyawan.access');
    // ... rute karyawan lainnya ...

    // --- RUTE SPPD (UNTUK PRIBADI) ---
    Route::resource('sppd', SppdController::class)->only(['index', 'create', 'store']);
    Route::get('sppd/download/{sppd}', [SppdController::class, 'download'])->name('sppd.download');
    
    // --- RUTE BARU UNTUK PERSETUJUAN SPPD ---
    Route::get('/sppd-approvals', [SppdApprovalController::class, 'index'])->name('sppd.approvals.index');
    Route::put('/sppd-approvals/{sppd}', [SppdApprovalController::class, 'update'])->name('sppd.approvals.update');

    // --- RUTE CUTI (UNTUK PRIBADI) ---
    Route::resource('cuti', CutiController::class)->only(['index', 'create', 'store', 'show']);
    Route::delete('/cuti/{cuti}/cancel', [CutiController::class, 'cancel'])->name('cuti.cancel');
    Route::get('/cuti/{cuti}/download', [CutiController::class, 'download'])->name('cuti.download');

    // --- RUTE PERSETUJUAN CUTI ---
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::put('/approval/{cuti}', [ApprovalController::class, 'update'])->name('approvals.update');
    
    // === BAGIAN YANG DIPERBAIKI ===
    // Nama 'downloadLaporan' diubah menjadi 'downloadReport'
    Route::get('/approval/laporan/download', [ApprovalController::class, 'downloadReport'])->name('approvals.downloadReport');
    
    // Rute Kalender
    Route::resource('kalender', KalenderController::class);

    // Rute SP
    Route::resource('sp', SPController::class)->middleware('check.peringatan.access');
    // ... rute SP lainnya ...

    Route::get('/cek-php', function () {
        phpinfo();
    });
    Route::resource('sp', SPController::class);
    Route::get('sp/download/{sp}', [SPController::class, 'download'])->name('sp.download');
    Route::get('sp/download-bukti/{sp}', [SPController::class, 'downloadBukti'])->name('sp.downloadBukti');
    Route::get('cari-karyawan', [SPController::class, 'cariKaryawan'])->name('cari-karyawan');


    // Rute Notifikasi
    Route::get('/notifications', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifications/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.mark-all-read');
    Route::post('/notifications/{notification}/mark-read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.mark-single-read');

    // ... rute kalender lainnya ...
});

require __DIR__.'/auth.php';

