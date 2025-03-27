<?php

use App\Http\Controllers\KendaraanController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [KendaraanController::class, 'index']);
Route::get('/kendaraan/data', [KendaraanController::class, 'getData']);
Route::get('/overview', [KendaraanController::class, 'kendaraan']);
Route::put('/kendaraan/update', [KendaraanController::class, 'update']);
