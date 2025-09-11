<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\KaryawanController;

Route::get('/', function () {
    return view('pages.index');
});


// Route::get('/karyawan', [KaryawanController::class, 'index'])->name('pages.karyawan.read');');

