<?php

namespace App\Helpers;

use Illuminate\Contracts\Filesystem\Filesystem;

class WpaSupplicantHelper
{

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function writeWpaSupplicant(array $savedWifiNetworks): void
    {
        if (app()->environment('local')) {
            return;
        }
        exec('sudo chmod 777 /etc/wpa_supplicant/wpa_supplicant.conf');

        $contents = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev
update_config=1
country=NL

";
        foreach ($savedWifiNetworks as $savedWifiNetwork) {
            $contents .= "
network={
    ssid=\"{$savedWifiNetwork['ssid']}\"
    psk=\"{$savedWifiNetwork['password']}\"
    id_str=\"{$savedWifiNetwork['ssid']}\"
}

";
        }
        $this->filesystem->put('wpa_supplicant.conf', $contents);

        exec('sudo wpa_cli -i wlan0 reconfigure || ( systemctl restart dhcpcd; wpa_cli -i wlan0 reconfigure; )');
    }

}
