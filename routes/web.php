<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(["prefix" => getLanguagePrefix()], function() {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('fields', [\App\Http\Controllers\FieldController::class, 'fields'])->name('fields.index');
    //Route::get('fields/{field}/edit', [\App\Http\Controllers\FieldController::class, 'editField'])->name('field.edit');
    Route::get('fields/{field}/download', [\App\Http\Controllers\FieldController::class, 'downloadField'])->name('field.download');
    Route::get('cerea', [\App\Http\Controllers\CereaController::class, 'index'])->name('cerea.index');
    Route::get('cerea/backup', [\App\Http\Controllers\CereaController::class, 'backup'])->name('cerea.backup');
    Route::get('cerea/app/{version}/download', [\App\Http\Controllers\CereaController::class, 'appDownload'])->name('cerea.app-download');

    Route::get('settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\SettingsController::class, 'save'])->name('settings.save');
    Route::get('update', [\App\Http\Controllers\UpdateController::class, 'update'])->name('update.update');

    Route::get('wifi', [\App\Http\Controllers\WiFiController::class, 'index'])->name('wifi.index');
    Route::post('wifi', [\App\Http\Controllers\WiFiController::class, 'save'])->name('wifi.save');
    Route::get('wifi/delete/{ssid}', [\App\Http\Controllers\WiFiController::class, 'delete'])->name('wifi.delete');

    Route::get('logs', function () {
        return view('logs');
    })->name('logs');
});
