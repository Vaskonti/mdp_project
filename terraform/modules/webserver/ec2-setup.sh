#!/bin/bash
# Cloning project
sudo mkdir /var/www/mdp_project && 
sudo git clone https://github.com/VasilHristovDev/mdp_project /var/www/mdp_project && 

# Installing dependencies
sudo apt update && sudo apt upgrade -y &&
sudo apt install software-properties-common -y &&
sudo add-apt-repository ppa:ondrej/php &&
sudo apt update && sudo apt upgrade -y &&
sudo apt install php8.2-fpm -y &&
sudo apt install php8.2-common php8.2-mysql php8.2-xml php8.2-xmlrpc php8.2-curl php8.2-gd php8.2-imagick php8.2-cli php8.2-dev php8.2-imap php8.2-mbstring php8.2-opcache php8.2-soap php8.2-zip php8.2-redis php8.2-intl -y &&
sudo apt install composer nginx php-mongo -y &&

# Project setup
cd /var/www/mdp_project &&
sudo cp .env.ci .env &&
composer install &&
php artisan key:generate &&
php artisan migrate &&

# Nginx setup
sudo cp /var/www/mdp_project/terraform/modules/webserver/nginx.conf /etc/nginx/sites-available/mdp_project &&
cd /etc/nginx/sites-enabled &&
sudo ln -s /etc/nginx/sites-available/mdp_project &&
sudo systemctl start nginx