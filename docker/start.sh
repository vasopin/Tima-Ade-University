#!/bin/sh
set -eu

rm -f bootstrap/cache/*.php

php artisan migrate --force
php artisan config:cache
php artisan view:cache
php artisan optimize
php artisan storage:link --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"