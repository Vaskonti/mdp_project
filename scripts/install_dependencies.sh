#!/bin/bash

# Install dependencies
sudo rm -rf /var/www/mdp_project ||

# Installing dependencies
sudo apt update || sudo apt upgrade -y ||
sudo apt install software-properties-common -y ||
sudo add-apt-repository ppa:ondrej/php -y ||
sudo apt update || sudo apt upgrade -y ||
sudo apt install php8.2-fpm php8.2-common php8.2-mysql php8.2-xml php8.2-xmlrpc php8.2-curl php8.2-gd php8.2-imagick php8.2-cli php8.2-dev php8.2-imap php8.2-mbstring php8.2-opcache php8.2-soap php8.2-zip php8.2-redis php8.2-intl php8.2-mongodb composer nginx -y ||

# Installing AWS CLI
sudo apt install unzip ||
sudo curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip" ||
sudo unzip -u awscliv2.zip ||
sudo ./aws/install --update
