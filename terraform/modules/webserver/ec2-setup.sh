#!/bin/bash
# Cloning project
sudo mkdir /var/www/mdp_project && 
git clone https://github.com/VasilHristovDev/mdp_project /var/www/mdp_project && 

# Disabling and removing apache2
sudo systemctl stop apache2 &&
sudo systemctl disable apache2 &&
sudo apt purge apache2 && sudo apt autoremove &&

# Installing dependencies
sudo apt update && sudo apt upgrade -y &&
sudo apt install software-properties-common &&
sudo add-apt-repository ppa:ondrej/php &&
sudo apt update && sudo apt upgrade -y &&
sudo apt install php8.2-fpm &&
sudo apt install php8.2-common php8.2-mysql php8.2-xml php8.2-xmlrpc php8.2-curl php8.2-gd php8.2-imagick php8.2-cli php8.2-dev php8.2-imap php8.2-mbstring php8.2-opcache php8.2-soap php8.2-zip php8.2-redis php8.2-intl -y &&
sudo apt install composer nginx php8.2-mongo -y &&

# Project setup
cd /var/www/mdp_project &&
cp .env.ci .env &&
composer install &&
php artisan key:generate &&
php artisan migrate &&

# Nginx setup
cp /var/www/mdp_project/terraform/modules/webserver/nginx.conf /etc/nginx/sites-available/mdp-project &&
cd /etc/nginx/sites-enabled &&
ln -s /etc/nginx/sites-available/mdp_project &&
sudo systemctl start nginx
