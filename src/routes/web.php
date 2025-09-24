<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SppdController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\RiwayatJabatanController;
use App\Http\Controllers\SPController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/dashboard', function () {
    return view('layouts.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Rute Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // tambahan
    Route::get('/profile/detail', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/tambahjabatan/{id}', [UserController::class, 'jabatan'])->name('karyawan.tambahjabatan'); // <-- Tambahkan nama ini
Route::put('/updatejabatan/{id}', [UserController::class, 'updatejabatan'])->name('karyawan.updatejabatan');
Route::get('/editpi/{id}', [UserController::class, 'editpi'])->name('karyawan.editpi');
Route::put('/updatepi/{id}', [UserController::class, 'updatepi'])->name('karyawan.updatepi');
Route::get('/editkep/{id}', [UserController::class, 'editkep'])->name('karyawan.editkep');
Route::put('/updatekep/{id}', [UserController::class, 'updatekep'])->name('karyawan.updatekep');
Route::get('/karyawan/{karyawan}/riwayat/{riwayat}/edit', [RiwayatJabatanController::class, 'edit'])->name('riwayat.edit');
Route::put('/karyawan/{karyawan}/riwayat/{riwayat}/update', [RiwayatJabatanController::class, 'update'])->name('riwayat.update');

    Route::resource('karyawan', UserController::class);
    Route::resource('sppd', SppdController::class);

    // ✅ Route ini sudah benar karena menggunakan Route Model Binding
    //    Parameter di URL {sppd} akan otomatis mencari model Sppd
    Route::patch('/sppd/{sppd}/status', [SppdController::class, 'updateStatus'])->name('sppd.updateStatus');

    // Route untuk halaman download
    Route::get('sppd/download/{sppd}', [SppdController::class, 'download'])->name('sppd.download');
    Route::get('sppd/{sppd}/generate', [SppdController::class, 'generateSuratPdf'])->name('sppd.generate');

    Route::get('/tambahjabatan/{id}', [UserController::class, 'jabatan'])->name('karyawan.tambahjabatan');
    Route::put('/updatejabatan/{id}', [UserController::class, 'updatejabatan'])->name('karyawan.updatejabatan');

    Route::resource('cuti', CutiController::class);
    // PINDAHKAN SEMUA RUTE APLIKASI KE SINI
    Route::get('/tambahjabatan/{id}', [UserController::class, 'jabatan'])->name('karyawan.tambahjabatan');
    Route::put('/updatejabatan/{id}', [UserController::class, 'updatejabatan'])->name('karyawan.updatejabatan');

    Route::resource('karyawan', UserController::class);
    
    Route::resource('sppd', SppdController::class);
    Route::patch('/sppd/{id}/status', [SppdController::class, 'updateStatus'])->name('sppd.updateStatus');
    Route::get('/sppd/{id}/download', [SppdController::class, 'download'])->name('sppd.download');
    Route::get('sppd/{id}/generate', [SppdController::class, 'generate'])->name('sppd.generate');

    Route::resource('cuti', CutiController::class);

    // PASTIKAN BARIS INI ADA PERSIS SEPERTI INI
    Route::get('cari', [SPController::class, 'cariKaryawan'])->name('cari');
    
    Route::resource('sp', SPController::class);

    Route::resource('kalender', KalenderController::class);
});
Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';
