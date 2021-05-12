<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/wifi', [\App\Http\Controllers\WiFiController::class, 'wifi'])->name('api.wifi');
Route::get('/checkUpdate', [\App\Http\Controllers\UpdateController::class, 'checkUpdate'])->name('api.checkUpdate');
Route::get('/systemdata', [\App\Http\Controllers\DashboardController::class, 'systemdata'])->name('api.systemdata');
