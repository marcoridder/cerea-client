#!/bin/bash

echo '################################'
echo 'INSTALLING WEBSERVER'
echo '################################'

sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list
sudo apt update
sudo apt install -y vim apache2 php8.0 php8.0-xml php8.0-mbstring php8.0-curl php8.0-zip
sudo a2enmod rewrite

# Run apache as user pi
sudo sed -i 's/export APACHE_RUN_USER=www-data/export APACHE_RUN_USER=pi/g' /etc/apache2/envvars
sudo sed -i 's/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP=pi/g' /etc/apache2/envvars
sudo service apache2 restart

echo '################################'
echo 'INSTALLING DEPENDENCIES'
echo '################################'

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
php -r "unlink('composer-setup.php');"


echo '################################'
echo 'INSTALLING CEREACLIENT'
echo '################################'

sudo rm -rf /srv/cereaclient
sudo mkdir /srv/cereaclient && sudo chown pi:pi /srv/cereaclient
git clone https://github.com/marcoridder/cerea-client.git /srv/cereaclient
cd /srv/cereaclient || exit
git checkout v1.0.0;
cp .env.prod .env
cp resources/configfiles/appconfig.conf config/appconfig.php

/usr/local/bin/composer install --no-dev
php artisan key:generate

echo '########################'
echo 'COPY CUSTOM CONFIG FILES'
echo '########################'
sudo cp resources/configfiles/dhcpcd.conf /etc/dhcpcd.conf
sudo cp resources/configfiles/opcache.ini /etc/php/8.0/mods-available/opcache.ini

sudo cp resources/configfiles/cereaclient.conf /etc/apache2/sites-available/cereaclient.conf
sudo a2dissite 000-default.conf
sudo a2ensite cereaclient.conf
sudo service apache2 restart
echo "\n\nReady, now browse to http://192.168.1.222"

