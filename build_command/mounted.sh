#!/bin/bash

docker cp docker/provision/env/.env.local laravel_php:/repo/docker/src/system/.env
wait
docker exec -it laravel_php composer install
wait
docker exec -it laravel_php composer update
wait
docker exec -it laravel_php mysql -h "laravel_mariadb" -u "root" -p"Password01" -e "DROP DATABASE IF EXISTS app;
CREATE DATABASE app;"
wait
docker exec -it laravel_php php artisan migrate
wait
docker exec -it laravel_php php artisan db:seed