#!/bin/sh
set -e
cd /var/www/html

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views \
         storage/logs storage/app/public bootstrap/cache
touch database/database.sqlite

if [ ! -f .env ]; then cp .env.example .env; fi
if ! grep -q "^APP_KEY=base64" .env; then php artisan key:generate --force || true; fi

php artisan storage:link || true
php artisan migrate --force
chown -R www-data:www-data storage bootstrap/cache database

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf