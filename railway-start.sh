#!/bin/sh
set -e

php artisan storage:link || true
php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
