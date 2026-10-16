#!/bin/bash
set -e
PORT="${PORT:-8080}"
echo "Listen ${PORT}" > /etc/apache2/ports.conf
export PORT
cd /var/www/html
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
php artisan config:clear || true
php artisan view:clear || true
chown -R www-data:www-data storage bootstrap/cache
exec apache2-foreground
