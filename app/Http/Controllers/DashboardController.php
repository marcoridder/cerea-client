<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{

    public function __construct()
    {

    }

    public function index(): View
    {
        exec('iwgetid wlan0', $wlanResults);
        exec('ping -qc 1 google.com > /dev/null && echo ok || echo error', $pingResults);

        return view('dashboard')
            ->with('hasWifiNetworks', config('appconfig.wifi') ? count(config('appconfig.wifi')) >= 1 : false)
            ->with('connectedWifiNetwork', trim(str_replace(['wlan0     ESSID:', '"'], null, $wlanResults[0] ?? null)))
            ->with('hasInternet', ($pingResults[0] ?? null) === 'ok')
            ->with('systemData', $this->systemdata())
        ;
    }

    public function systemdata(): array
    {
        exec('sudo vcgencmd measure_temp', $temperatureResult);

        if (app()->environment('local')) {
            $temperatureResult = [
                "temp=".rand(1,100)."'C"
            ];
        }

        $cpuTemperature = trim(str_replace(['temp=', "'C"], null, $temperatureResult[0] ?? null));

        return [
            'cpuTemperature' => trim(str_replace(['temp=', "'C"], null, $temperatureResult[0] ?? null)),
            'cpuTemperatureClass' => ($cpuTemperature <= 60) ? 'success' : ($cpuTemperature <= 70 ? 'warning' : 'danger'),
        ];
    }

}
