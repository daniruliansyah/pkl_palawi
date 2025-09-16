<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SppdController;
use App\Http\Controllers\KalenderController;

Route::get('/', function () {
    return view('pages.index');
});

Route::get('/tambahjabatan/{id}', [UserController::class, 'jabatan'])->name('karyawan.tambahjabatan'); // <-- Tambahkan nama ini
Route::put('/updatejabatan/{id}', [UserController::class, 'updatejabatan'])->name('karyawan.updatejabatan');

Route::resource('karyawan', UserController::class);

Route::resource('sppd', SppdController::class);
Route::resource('kalender', KalenderController::class);

// Route::resource('sppd', SppdController::class)->middleware('auth');
