#!/bin/bash
publicIps=$(aws ec2 describe-instances --filters "Name=tag:Name,Values=webserver" --query "Reservations[*].Instances[*].PublicIpAddress" --output=text)

for ip in $publicIps
do
    echo "Connecting to $ip"
    ssh ubuntu@$ip 'sudo aws s3 cp s3://environment-laravel/env/.env /var/www/mdp_project/.env; cd /var/www/mdp_project; sudo composer install; sudo php artisan key:generate; exit'
done

echo "Done"
