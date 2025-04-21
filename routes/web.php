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

Route::middleware(['auth'])->group(function () {
    Route::get('/kendaraan', [KendaraanController::class, 'kendaraan']);
    Route::get('/kendaraan/data', [KendaraanController::class, 'getData']);
    Route::put('/kendaraan/update', [KendaraanController::class, 'update'])->name('kendaraan.update');
});

// login
Route::get('/login', [LoginController::class, 'index'])->name('login'); 
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



