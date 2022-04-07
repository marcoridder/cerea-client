<?php

use App\Http\Controllers;
use Illuminate\Routing\Router;
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

$router = app('router');

$router
    ->prefix(getLanguagePrefix())
    ->group(function (Router $router) {
        $router->get('/', [Controllers\DashboardController::class, 'index'])->name('dashboard.index');

        $router->get('fields', [Controllers\FieldController::class, 'fields'])->name('fields.index');
        //$router->get('fields/{field}/edit', [Controllers\FieldController::class, 'editField'])->name('field.edit');
        $router->get('fields/{field}/download', [Controllers\FieldController::class, 'downloadField'])->name('field.download');

        $router->get('cerea', [Controllers\CereaController::class, 'index'])->name('cerea.index');
        $router->get('cerea/backup', [Controllers\CereaController::class, 'backup'])->name('cerea.backup');
        $router->get('cerea/app/{version}/download', [Controllers\CereaController::class, 'appDownload'])->name('cerea.app-download');

        $router->get('settings', [Controllers\SettingsController::class, 'index'])->name('settings.index');
        $router->post('settings', [Controllers\SettingsController::class, 'save'])->name('settings.save');

        $router->get('update', [Controllers\UpdateController::class, 'update'])->name('update.update');

        $router->get('wifi', [Controllers\WiFiController::class, 'index'])->name('wifi.index');
        $router->post('wifi', [Controllers\WiFiController::class, 'save'])->name('wifi.save');
        $router->get('wifi/delete/{ssid}', [Controllers\WiFiController::class, 'delete'])->name('wifi.delete');

        $router->get('logs', function () {
            return view('logs');
        })->name('logs');
});
