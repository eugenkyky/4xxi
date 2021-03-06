#!/usr/bin/env bash
#PHP install
sudo apt-get update &&
sudo apt-get install python-software-properties -y &&
sudo LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php -y &&
sudo apt-get update &&
sudo apt-get install php7.0 php7.0-mysql curl git libpcre3 zip unzip php7.0-xml -y &&
(curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer) &&
#MYSQL install
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password qwer1234'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password qwer1234'
sudo apt-get install mysql-server -y &&
sudo apt-get install mysql-client -y &&
git clone https://github.com/eugenkyky/4xxi.git test_assignment &&
#composer get additional packages
cd test_assignment &&
mysql -uroot -pqwer1234 -e "CREATE DATABASE symfony;" &&
sudo composer install &&
#install db scheme
#create schema
sudo php bin/console doctrine:schema:update --force
sudo php bin/console server:run 0.0.0.0:8000
echo "Deploy task succeded" &&
exit 0








