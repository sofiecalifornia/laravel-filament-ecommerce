#!/bin/sh

composer self-update --2 --stable

php artisan down --render="errors::503" --refresh=5

composer clear-all

git pull origin main

composer i --no-interaction --prefer-dist --optimize-autoloader --no-dev

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service php8.3-fpm reload ) 9>/tmp/fpmlock

php artisan horizon:terminate
php artisan pulse:restart

composer reset

php artisan migrate --force

php artisan app:permissions-sync

php artisan up
