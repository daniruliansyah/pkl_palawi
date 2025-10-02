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

// Route untuk verifikasi surat via QR Code (bisa di luar auth jika diperlukan)
Route::get('/sppd/verifikasi/{id}', [SppdController::class, 'verifikasi'])->name('sppd.verifikasi');

Route::middleware('auth')->group(function () {
    // Rute Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/detail', [ProfileController::class, 'show'])->name('profile.show');

    // Rute Karyawan dan Riwayat Jabatan
    Route::resource('karyawan', UserController::class)->middleware('check.karyawan.access');
    Route::get('/tambahjabatan/{id}', [UserController::class, 'jabatan'])->name('karyawan.tambahjabatan');
    // ... rute karyawan lainnya ...

    // Rute SPPD
    Route::resource('sppd', SppdController::class);
    Route::patch('/sppd/{sppd}/status', [SppdController::class, 'updateStatus'])->name('sppd.updateStatus');
    Route::get('sppd/download/{sppd}', [SppdController::class, 'download'])->name('sppd.download');

    // --- RUTE CUTI (UNTUK PRIBADI) ---
    Route::resource('cuti', CutiController::class)->only(['index', 'create', 'store', 'show']);
    Route::delete('/cuti/{cuti}/cancel', [CutiController::class, 'cancel'])->name('cuti.cancel');

    // --- RUTE BARU UNTUK PERSETUJUAN ---
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::put('/approval/{cuti}', [ApprovalController::class, 'update'])->name('approvals.update');
    Route::get('/approval/laporan/download', [ApprovalController::class, 'downloadReport'])->name('approvals.downloadReport');
    
    // Rute Kalender
    Route::resource('kalender', KalenderController::class);

    // Rute SP
    Route::resource('sp', SPController::class)->middleware('check.peringatan.access');
    Route::get('sp/download/{sp}', [SPController::class, 'download'])->name('sp.download')->middleware('check.peringatan.access');
    Route::get('sp/download-bukti/{sp}', [SPController::class, 'downloadBukti'])->name('sp.downloadBukti')->middleware('check.peringatan.access');
    Route::get('cari-karyawan', [SPController::class, 'cariKaryawan'])->name('cari-karyawan')->middleware('check.peringatan.access');

    Route::get('/cek-php', function () {
        phpinfo();
    });

    // Route::get('/calender', function () {
    //     return view('pages.calendar.index');
    // })->middleware('auth')->name('personal.notes');

    // Route::middleware(['auth'])->prefix('calendar')->group(function () {
    //     // 1. Tampilan Utama Kalender (Blade View)
    //     // Route ini akan menampilkan file blade kalender.
    //     // Kita akan buat fungsi 'showCalendar' di Controller nanti.
    //     Route::get('/calendar', [KalenderController::class, 'showCalendar'])->name('calendar.index');
        
    //     // 2. API: Mengambil semua notes user yang sedang login (READ)
    //     Route::get('/notes', [KalenderController::class, 'index'])->name('calendar.api.index');
        
    //     // 3. API: Menyimpan atau mengupdate notes (CREATE/UPDATE)
    //     Route::post('/notes', [KalenderController::class, 'storeOrUpdate'])->name('calendar.api.store');

    //     // 4. API: Menghapus catatan (DELETE)
    //     Route::delete('/notes/{id}', [KalenderController::class, 'destroy'])->name('calendar.api.destroy');
    // });

    Route::get('/calendar/calendar', [KalenderController::class, 'showCalendar'])->name('calendar.index');
    Route::get('/calendar/notes', [KalenderController::class, 'index'])->name('calendar.api.index');
    Route::post('/calendar/notes', [KalenderController::class, 'storeOrUpdate'])->name('calendar.api.store');
    Route::delete('/calendar/notes/{id}', [KalenderController::class, 'destroy'])->name('calendar.api.destroy');


});

require __DIR__.'/auth.php';
