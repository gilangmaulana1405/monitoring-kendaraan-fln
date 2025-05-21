<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\KendaraanController;


Route::get('/', function () {
    return redirect('/login');
});

Route::get('/monitoring-kendaraan', [KendaraanController::class, 'index']);

Route::middleware(['auth', 'role:Admin GA, Staff GA'])->group(function () {
    // admin
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::get('/users/list', [AdminController::class, 'listUsers'])->name('list.users');
    Route::get('/get-data-users', [AdminController::class, 'getDataUsers'])->name('list.users.data');
    Route::post('/users/ganti-password', [AdminController::class, 'gantiPassword'])->name('users.gantiPassword');
    Route::get('/kendaraan/list', [AdminController::class, 'listKendaraan'])->name('list.kendaraan');
    Route::get('/get-data-kendaraan', [AdminController::class, 'getDataKendaraan'])->name('list.kendaraan.data');
    Route::get('/kendaraan/history', [AdminController::class, 'historyKendaraan'])->name('history.kendaraan');
    Route::get('/get-data-history-kendaraan', [AdminController::class, 'getDatahistoryKendaraan'])->name('history.kendaraan.data');
});

Route::middleware(['auth', 'role:Admin GA,Staff GA,Security'])->group(function () {
    // inputan kendaraan (petugas security)
    Route::get('/kendaraan', [KendaraanController::class, 'kendaraan']);
    Route::put('/kendaraan/update', [KendaraanController::class, 'update'])->name('kendaraan.update');
    Route::get('/kendaraan/data', [KendaraanController::class, 'getData']);
});

// login
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
