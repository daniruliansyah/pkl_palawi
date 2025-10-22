<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SppdController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\RiwayatJabatanController;
use App\Http\Controllers\SPController;
use App\Http\Controllers\ApprovalController; // Controller Cuti
use App\Http\Controllers\GajiController;
use App\Http\Controllers\SPApprovalController; // Controller SP BARU
use App\Http\Controllers\SppdApprovalController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\RiwayatPendidikanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Rute Dashboard
Route::get('/dashboard', function () {
    return view('pages.dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rute verifikasi publik (Tidak memerlukan autentikasi)
Route::get('/sppd/verifikasi/{id}', [SppdController::class, 'verifikasi'])->name('sppd.verifikasi');
Route::get('/sp/verifikasi/{id}', [SPController::class, 'verifikasi'])->name('sp.verifikasi'); // Pastikan ini tidak duplikat
Route::get('/cuti/verifikasi/{id}', [CutiController::class, 'verifikasi'])->name('cuti.verifikasi');

Route::middleware('auth')->group(function () {
    // -----------------------------------------------------------------
    // --- RUTE PROFIL & KARYAWAN ---
    // -----------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/detail', [ProfileController::class, 'show'])->name('profile.show');

    Route::resource('karyawan', UserController::class)->middleware('check.karyawan.access');
    Route::get('/tambahjabatan/{id}', [UserController::class, 'jabatan'])->name('karyawan.tambahjabatan');
    Route::put('/updatejabatan/{id}', [UserController::class, 'updatejabatan'])->name('karyawan.updatejabatan');
    Route::get('/editpi/{id}', [UserController::class, 'editpi'])->name('karyawan.editpi');
    Route::put('/updatepi/{id}', [UserController::class, 'updatepi'])->name('karyawan.updatepi');
    Route::get('/editkep/{id}', [UserController::class, 'editkep'])->name('karyawan.editkep')->middleware('check.karyawan.access');
    Route::put('/updatekep/{id}', [UserController::class, 'updatekep'])->name('karyawan.updatekep');
    Route::get('/karyawan/{karyawan}/riwayat/{riwayat}/edit', [RiwayatJabatanController::class, 'edit'])->name('riwayat.edit')->middleware('check.karyawan.access');
    Route::put('/karyawan/{karyawan}/riwayat/{riwayat}/update', [RiwayatJabatanController::class, 'update'])->name('riwayat.update')->middleware('check.karyawan.access');
    Route::get('karyawan-cari', [UserController::class, 'cariKaryawan'])->name('karyawan.cari')->middleware('check.karyawan.access');

    // -----------------------------------------------------------------
    // --- RUTE SPPD ---
    // -----------------------------------------------------------------
    Route::resource('sppd', SppdController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('sppd/download/{sppd}', [SppdController::class, 'download'])->name('sppd.download');
    // PERSETUJUAN SPPD
    Route::get('/sppd-approvals', [SppdApprovalController::class, 'index'])->name('sppd.approvals.index');
    Route::put('/sppd-approvals/{sppd}', [SppdApprovalController::class, 'update'])->name('sppd.approvals.update');
    Route::get('/sppd-approvals/laporan/download', [SppdApprovalController::class, 'downloadReport'])->name('sppd.downloadReport');

    // Rute untuk Fitur Pertanggungjawaban SPPD
    Route::get('/pertanggungjawaban/{sppd}/create', [\App\Http\Controllers\PertanggungjawabanController::class, 'create'])->name('pertanggungjawaban.create');
    Route::post('/pertanggungjawaban/{sppd}', [\App\Http\Controllers\PertanggungjawabanController::class, 'store'])->name('pertanggungjawaban.store');
    Route::get('/pertanggungjawaban/{pertanggungjawaban}/download', [\App\Http\Controllers\PertanggungjawabanController::class, 'download'])->name('pertanggungjawaban.download');

    // --- RUTE CUTI (UNTUK PRIBADI) ---
    Route::resource('cuti', CutiController::class)->only(['index', 'create', 'store', 'show']);
    Route::put('/cuti/{cuti}/updatestatus', [CutiController::class, 'updateStatus'])->name('cuti.updateStatus');
    Route::delete('/cuti/{cuti}/cancel', [CutiController::class, 'cancel'])->name('cuti.cancel');
    Route::get('/cuti/{cuti}/download', [CutiController::class, 'download'])->name('cuti.download');
    // PERSETUJUAN CUTI
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::put('/approval/cuti/{cuti}', [ApprovalController::class, 'update'])->name('approvals.cuti.update');
    Route::get('/approval/laporan/download', [ApprovalController::class, 'downloadReport'])->name('approvals.downloadReport');

   // -----------------------------------------------------------------
    // --- RUTE SURAT PERINGATAN (SP) ---
    // -----------------------------------------------------------------

    // GRUP UNTUK MANAJEMEN SP (Membuat, Melihat Daftar, Detail)
    Route::prefix('sp')->name('sp.')->group(function () {
    Route::get('/', [SPController::class, 'index'])->name('index');
    Route::get('/create', [SPController::class, 'create'])->name('create');
    
    // PINDAHKAN ROUTE STATIS KE ATAS SINI
    Route::get('/cari-karyawan', [SPController::class, 'cariKaryawan'])->name('cari-karyawan');
    
    Route::post('/', [SPController::class, 'store'])->name('store');
    
    // ROUTE DINAMIS BIARKAN DI BAWAH
    Route::get('/{sp}', [SPController::class, 'show'])->name('show');
    Route::get('/{id}/download', [SPController::class, 'download'])->name('download');
    });

    // GRUP KHUSUS UNTUK PROSES PERSETUJUAN SP
    Route::prefix('sp-approvals')->name('sp.approvals.')->group(function () {
        Route::get('/', [SPApprovalController::class, 'index'])->name('index');
        Route::post('/{sp}/update-status', [SPApprovalController::class, 'updateStatus'])->name('update'); // Perhatikan 'updateStatus'
        Route::get('/report/download', [SPApprovalController::class, 'downloadReport'])->name('downloadReport');
    });

    // -----------------------------------------------------------------
    // --- RUTE LAINNYA ---
    // -----------------------------------------------------------------
    Route::get('/cek-php', function () {
        phpinfo();
    });

    // NOTIFIKASI
    Route::get('/notifications', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifications/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.mark-all-read');
    Route::post('/notifications/{notification}/mark-read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.mark-single-read');

    // KALENDER
    Route::get('/calendar/calendar', [KalenderController::class, 'showCalendar'])->name('calendar.index');
    Route::get('/calendar/notes', [KalenderController::class, 'index'])->name('calendar.api.index');
    Route::post('/calendar/notes', [KalenderController::class, 'storeOrUpdate'])->name('calendar.api.store');
    Route::delete('/calendar/notes/{id}', [KalenderController::class, 'destroy'])->name('calendar.api.destroy');

    // Gunakan GET untuk menampilkan halaman form
    Route::get('/gaji/create/{user}', [GajiController::class, 'create'])->name('gaji.create');
    Route::post('/gaji', [GajiController::class, 'store'])->name('gaji.store');
    // Route untuk halaman cetak (Anda perlu membuat metode 'cetak' di controller)
    Route::get('/gaji/{gaji}/cetak', [GajiController::class, 'cetakSlip'])->name('gaji.cetak');
    Route::get('/karyawan/{user}/gaji', [GajiController::class, 'indexForKaryawan'])->name('gaji.indexForKaryawan');
    Route::delete('/gaji/{gaji}', [GajiController::class, 'destroy'])->name('gaji.destroy');
    Route::get('karyawan-cari', [UserController::class, 'cariKaryawan'])->name('karyawan.cari')->middleware('check.karyawan.access');

    // =================================================================
    // --- RUTE RIWAYAT PENDIDIKAN (BARU) ---
    // =================================================================
    // Grup ini menangani route yang terkait langsung dengan Karyawan (create, store)
    Route::prefix('karyawan/{karyawan}/pendidikan')->name('karyawan.pendidikan.')->group(function () {
        Route::get('/create', [RiwayatPendidikanController::class, 'create'])->name('create');
        Route::post('/', [RiwayatPendidikanController::class, 'store'])->name('store');
    });

    // Grup ini menangani route yang terkait langsung dengan data Pendidikan (edit, update, destroy)
    Route::prefix('pendidikan')->name('pendidikan.')->group(function () {
        Route::get('/{pendidikan}/edit', [RiwayatPendidikanController::class, 'edit'])->name('edit')->middleware('check.karyawan.access');
        Route::put('/{pendidikan}', [RiwayatPendidikanController::class, 'update'])->name('update')->middleware('check.karyawan.access');
        Route::delete('/{pendidikan}', [RiwayatPendidikanController::class, 'destroy'])->name('destroy')->middleware('check.karyawan.access');
    });
    // =================================================================
});

require __DIR__.'/auth.php';
