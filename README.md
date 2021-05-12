# Cerea client

### An easy to use and easy to install web frontend to help you configure Wifi hotspot and get more out of Cerea GPS 

#### For use on Raspberry Pi only

## FrontEnd
<img src="https://raw.githubusercontent.com/marcoridder/cerea-client/master/images/dashboard.png" alt="Dashboard" width="300">
<img src="https://raw.githubusercontent.com/marcoridder/cerea-client/master/images/fields.png" alt="Fields" width="300">
<img src="https://raw.githubusercontent.com/marcoridder/cerea-client/master/images/wifi.png" alt="Wifi" width="300">
<img src="https://raw.githubusercontent.com/marcoridder/cerea-client/master/images/wifi-add.png" alt="Wifi add" width="300">
<img src="https://raw.githubusercontent.com/marcoridder/cerea-client/master/images/cerea.png" alt="Cerea" width="300">

## Easy installation
+ Connect your Rapsberry Pi to the internet
+ Login at the Raspberry and:
  
   ```bash
   $ cd ~
   $ wget https://raw.githubusercontent.com/marcoridder/cerea-client/master/tools/install.sh -O install.sh
   $ sh ./install.sh
   ```

+ The script installs a webserver and the Cerea client software.

## History
See the [changelog](./CHANGELOG.md)

## Feature plans

+ Share or synchronize fields, (AB) lines, vehicles and implements between multiple Cerea systems

## Bugs

Have a bug or a feature request?
[Please open a new issue](https://github.com/marcoridder/cerea-client/issues).

## License

Cerea client is open-sourced software licensed under the [MIT License](./LICENSE).

Cerea client uses some parts of others software:
+ [Laravel Framework](https://github.com/laravel/laravel)
