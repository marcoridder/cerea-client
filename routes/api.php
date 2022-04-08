<?php

use Illuminate\Routing\Router;
use App\Http\Controllers;

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

$router = app('router');

$router
    ->prefix(getLanguagePrefix())
    ->group(function (Router $router) {
        $router->get('/wifi', [Controllers\WiFiController::class, 'wifi'])->name('api.wifi');
        $router->get('/checkUpdate', [Controllers\UpdateController::class, 'checkUpdate'])->name('api.checkUpdate');
        $router->get('/systemdata', [Controllers\DashboardController::class, 'systemdata'])->name('api.systemdata');
    });
