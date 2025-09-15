<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SppdController;
use App\Http\Controllers\KalenderController;

Route::get('/', function () {
    return view('pages.index');
});

Route::resource('karyawan', UserController::class);

Route::resource('sppd', SppdController::class);
Route::resource('kalender', KalenderController::class);

// Route::resource('sppd', SppdController::class)->middleware('auth');
