#!/bin/bash
# Cloning project
git clone https://github.com/VasilHristovDev/mdp_project /var/www/

# Disabling and removing apache2
sudo systemctl stop apache2
sudo systemctl disable apache2
sudo apt purge apache2 && sudo apt autoremove

# Installing dependencies
sudo apt update
sudo apt install nginx php8.2 php8.2-fpm php8.2-redis php8.2-mongodb php-mysql composer

# Project setup
cd /var/www/mdp_project
cp .env.ci .env
composer install
php artisan key:generate
php artisan migrate

# Nginx setup
cp /var/www/mdp_project/terraform/modules/webserver/nginx.conf /etc/nginx/sites-available/mdp-project
cd /etc/nginx/sites-enabled
ln -s /etc/nginx/sites-available/mdp_project
systemctl start nginx.service
