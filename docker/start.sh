#!/bin/sh
set -eu

rm -f bootstrap/cache/*.php

php artisan migrate --force

# Populate the repository's initial roles, accounts, and demo data on a fresh or
# partially initialized database. The program catalog is created by the full
# seeder and is a reliable completion marker after an interrupted migration.
if [ "$(php artisan tinker --execute="echo \App\Models\Program::count();" 2>/dev/null)" = "0" ]; then
  php artisan db:seed --force
fi

php artisan config:cache
php artisan view:cache
php artisan optimize
php artisan storage:link --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"