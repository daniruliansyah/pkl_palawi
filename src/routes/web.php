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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('layouts.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Rute Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Karyawan dan Riwayat Jabatan
    Route::resource('karyawan', UserController::class);
    Route::get('/tambahjabatan/{id}', [UserController::class, 'jabatan'])->name('karyawan.tambahjabatan');
    Route::put('/updatejabatan/{id}', [UserController::class, 'updatejabatan'])->name('karyawan.updatejabatan');
    Route::get('/editpi/{id}', [UserController::class, 'editpi'])->name('karyawan.editpi');
    Route::put('/updatepi/{id}', [UserController::class, 'updatepi'])->name('karyawan.updatepi');
    Route::get('/editkep/{id}', [UserController::class, 'editkep'])->name('karyawan.editkep');
    Route::put('/updatekep/{id}', [UserController::class, 'updatekep'])->name('karyawan.updatekep');
    Route::get('/karyawan/{karyawan}/riwayat/{riwayat}/edit', [RiwayatJabatanController::class, 'edit'])->name('riwayat.edit');
    Route::put('/karyawan/{karyawan}/riwayat/{riwayat}/update', [RiwayatJabatanController::class, 'update'])->name('riwayat.update');

    // Rute SPPD
    Route::resource('sppd', SppdController::class);
    Route::patch('/sppd/{sppd}/status', [SppdController::class, 'updateStatus'])->name('sppd.updateStatus');
    Route::get('sppd/download/{sppd}', [SppdController::class, 'download'])->name('sppd.download');

    // Rute Cuti
    Route::resource('cuti', CutiController::class);
    Route::put('/cuti/{cuti}/update-status', [CutiController::class, 'updateStatus'])->name('cuti.updateStatus');
    Route::delete('/cuti/{cuti}/cancel', [CutiController::class, 'cancel'])->name('cuti.cancel');

    // Rute SP dan Kalender
    Route::get('cari', [SPController::class, 'cariKaryawan'])->name('cari');
    Route::resource('sp', SPController::class);
    Route::resource('kalender', KalenderController::class);
});

require __DIR__.'/auth.php';
