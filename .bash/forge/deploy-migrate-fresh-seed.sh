#!/bin/sh

cd $FORGE_SITE_PATH

$FORGE_PHP artisan down --render="errors::503" --refresh=5

$FORGE_COMPOSER clear-all

git pull origin $FORGE_SITE_BRANCH

$FORGE_COMPOSER i --no-interaction --prefer-dist --optimize-autoloader

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

$FORGE_PHP artisan env:decrypt --env=production --filename=.env --force

$FORGE_PHP artisan horizon:terminate
$FORGE_PHP artisan pulse:restart

$FORGE_PHP artisan migrate:fresh --seed --force

$FORGE_COMPOSER i --no-dev --no-interaction --prefer-dist --optimize-autoloader
$FORGE_COMPOSER reset

$FORGE_PHP artisan up
