<?php

namespace App\Providers;

use App\Helpers\WpaSupplicantHelper;
use App\Http\Controllers\CereaController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\WiFiController;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(FieldController::class)
            ->needs(Filesystem::class)
            ->give(function () {
                return Storage::disk('cerea_data');
            });

        $this->app->when(CereaController::class)
            ->needs(Filesystem::class)
            ->give(function () {
                return Storage::disk('cerea');
            });

        $this->app->when(SettingsController::class)
            ->needs(Filesystem::class)
            ->give(function () {
                return Storage::disk('config');
            });

        $this->app->when(WiFiController::class)
            ->needs(Filesystem::class)
            ->give(function () {
                return Storage::disk('config');
            });

        $this->app->when(WpaSupplicantHelper::class)
            ->needs(Filesystem::class)
            ->give(function () {
                return Storage::disk(app()->environment('local') ? 'local' : 'wpa_supplicant');
            });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
