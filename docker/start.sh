#!/bin/bash
set -e
PORT="${PORT:-8080}"
echo "Listen ${PORT}" > /etc/apache2/ports.conf
export PORT
cd /var/www/html
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
php artisan config:clear || true
php artisan view:clear || true
# Safe on every boot: only adds what registration needs if it is missing.
php artisan migrate --force --path=database/migrations/2026_11_02_000001_repair_registration_schema.php || true
chown -R www-data:www-data storage bootstrap/cache
exec apache2-foreground
