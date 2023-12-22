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

echo 'listen 80;
    server_name '"$1"';
    root /var/www/mdp_project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}' > /etc/nginx/sites-available/mdp_project

cd /etc/nginx/sites-enabled
ln -s /etc/nginx/sites-available/mdp_project
systemctl start nginx.service
