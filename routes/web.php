<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\KendaraanController;

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
    return redirect('/login');
});

Route::get('/monitoring-kendaraan', [KendaraanController::class, 'index']);
Route::get('/kendaraan/data', [KendaraanController::class, 'getData']);

// history
Route::get('/history-kendaraan', [KendaraanController::class, 'historyKendaraan'])->name('history.kendaraan');
Route::get('/get-data-history-kendaraan', [KendaraanController::class, 'getDatahistoryKendaraan'])->name('history.kendaraan.data');

Route::middleware(['auth'])->group(function () {
    // inputan kendaraan
    Route::get('/kendaraan', [KendaraanController::class, 'kendaraan']);
    Route::put('/kendaraan/update', [KendaraanController::class, 'update'])->name('kendaraan.update');
});

// login
Route::get('/login', [LoginController::class, 'index'])->name('login'); 
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



