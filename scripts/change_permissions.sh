#!/bin/bash

# Project setup
cd /var/www/mdp_project &&
sudo aws s3 cp s3://environment-laravel/env/.env . &&
printf "\n" | sudo composer install &&
sudo php artisan key:generate &&
sudo php artisan migrate ||
sudo chmod 777 -R /var/www/mdp_project/storage &&

# Nginx setup
sudo cp /var/www/mdp_project/terraform/modules/asg/nginx.conf /etc/nginx/sites-available/mdp_project &&
cd /etc/nginx/sites-enabled &&
sudo ln -s /etc/nginx/sites-available/mdp_project &&
sudo rm /etc/nginx/sites-available/default &&
sudo rm /etc/nginx/sites-enabled/default &&
sudo systemctl start nginx &&
sudo systemctl restart nginx
