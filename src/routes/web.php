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
use App\Http\Controllers\NotifikasiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    // Memanggil view konten yang benar
    return view('pages.dashboard.index');
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
    Route::get('/tambahjabatan/{id}', [UserController::class, 'jabatan'])->name('karyawan.tambahjabatan');
    Route::put('/updatejabatan/{id}', [UserController::class, 'updatejabatan'])->name('karyawan.updatejabatan');
    Route::get('/editpi/{id}', [UserController::class, 'editpi'])->name('karyawan.editpi');
    Route::put('/updatepi/{id}', [UserController::class, 'updatepi'])->name('karyawan.updatepi');
    Route::get('/editkep/{id}', [UserController::class, 'editkep'])->name('karyawan.editkep')->middleware('check.karyawan.access');
    Route::put('/updatekep/{id}', [UserController::class, 'updatekep'])->name('karyawan.updatekep');
    Route::get('/karyawan/{karyawan}/riwayat/{riwayat}/edit', [RiwayatJabatanController::class, 'edit'])->name('riwayat.edit')->middleware('check.karyawan.access');
    Route::put('/karyawan/{karyawan}/riwayat/{riwayat}/update', [RiwayatJabatanController::class, 'update'])->name('riwayat.update')->middleware('check.karyawan.access');

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
    Route::put('/cuti/{cuti}/update-status', [CutiController::class, 'updateStatus'])->name('cuti.updateStatus');
    Route::get('/cuti/verifikasi/{id}', [CutiController::class, 'verifikasi'])->name('cuti.verifikasi');

    // === BAGIAN YANG DIPERBAIKI ===
    // Nama 'downloadLaporan' diubah menjadi 'downloadReport'
    Route::get('/approval/laporan/download', [ApprovalController::class, 'downloadReport'])->name('approvals.downloadReport');

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
    Route::get('/calendar/calendar', [KalenderController::class, 'showCalendar'])->name('calendar.index');
    Route::get('/calendar/notes', [KalenderController::class, 'index'])->name('calendar.api.index');
    Route::post('/calendar/notes', [KalenderController::class, 'storeOrUpdate'])->name('calendar.api.store');
    Route::delete('/calendar/notes/{id}', [KalenderController::class, 'destroy'])->name('calendar.api.destroy');

    Route::get('karyawan-cari', [UserController::class, 'cariKaryawan'])->name('karyawan.cari')->middleware('check.karyawan.access');
});

require __DIR__.'/auth.php';

