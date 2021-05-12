<?php

namespace App\Http\Controllers;

use App\Helpers\WpaSupplicantHelper;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WiFiController extends Controller
{

    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var WpaSupplicantHelper
     */
    private WpaSupplicantHelper $wpaSupplicantHelper;

    public function __construct(Filesystem $filesystem, WpaSupplicantHelper $wpaSupplicantHelper)
    {
        $this->filesystem = $filesystem;
        $this->wpaSupplicantHelper = $wpaSupplicantHelper;
    }

    public function index(): View
    {
        exec('iwgetid wlan0', $exec_results);

        return view('wifi')
            ->with('savedWifiNetworks', config('appconfig.wifi') ?? [])
            ->with('connectedWifiNetwork', trim(str_replace(['wlan0     ESSID:', '"'], null, $exec_results[0] ?? null)))
        ;
    }

    public function save(Request $request): RedirectResponse
    {
        $data = $request->except('_token');
        $appConfig = config('appconfig');

        $appConfig['wifi'][$data['ssid']] = [
            'ssid' => $data['ssid'],
            'password' => $data['password'],

        ];

        $this->filesystem->put('appconfig.php', "<?php\n return ".var_export($appConfig, 1)." ;");

        $this->wpaSupplicantHelper->writeWpaSupplicant($appConfig['wifi']);

        return redirect(route('wifi.index'))->with('status', '"'.$data['ssid'].'" is toegevoegd');
    }

    public function delete($ssid)
    {
        $appConfig = config('appconfig');
        unset($appConfig['wifi'][$ssid]);
        $this->filesystem->put('appconfig.php', "<?php\n return ".var_export($appConfig, 1)." ;");

        $this->wpaSupplicantHelper->writeWpaSupplicant($appConfig['wifi']);

        return redirect(route('wifi.index'))->with('status', '"'.$ssid.'" is verwijderd');
    }

    public function wifi()
    {
        $ssids = [];

        if (app()->environment('local')) {
            sleep(1);
            $exec_results = [
                0 => '                   ESSID:"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"',
                1 => '                   ESSID:"Network 1"',
                2 => '                   ESSID:"Network 2"',
                3 => '                   ESSID:"Network 3"',
                4 => '                   ESSID:"Network 4"',
                5 => '                   ESSID:"Network 5"',
            ];
        }
        else {
            exec('sudo iwlist wlan0 scan|grep ESSID:| sort | uniq', $exec_results);
        }
        foreach ($exec_results as $exec_result) {
            $exec_result = trim($exec_result);
            if(stristr($exec_result, '\x00')) {
                continue;
            }
            $ssids[] = trim(str_replace(['ESSID:', '"'], null, $exec_result));
        }
        return array_filter($ssids);
    }
}
