<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('pages.index');
});

Route::get('/tambahjabatan/{id}', [UserController::class, 'jabatan'])->name('karyawan.tambahjabatan'); // <-- Tambahkan nama ini
Route::put('/updatejabatan/{id}', [UserController::class, 'updatejabatan'])->name('karyawan.updatejabatan');

Route::resource('karyawan', UserController::class);

