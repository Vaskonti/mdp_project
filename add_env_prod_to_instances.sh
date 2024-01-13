#!/bin/bash
privateIps=$(aws ec2 describe-instances --filters "Name=tag:Name,Values=webserver" --query "Reservations[*].Instances[*].PrivateIpAddress" --output=text)

for ip in $privateIps
do
    echo "Connecting to $ip"
    ssh -o StrictHostKeyChecking=no -i ~/.ssh/id_rsa ubuntu@$ip && sudo aws s3 cp s3://environment-laravel/env/.env /var/www/mdp_project/.env && cd /var/www/mdp_project && sudo composer install && sudo php artisan key:generate
done

echo "Done"
