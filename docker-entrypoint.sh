#!/bin/bash
set -e

# Support Render custom port or default to 80
PORT="${PORT:-80}"
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Log PHP errors to stderr so they show up in Render Logs
echo "php_flag display_errors Off" >> /etc/apache2/apache2.conf
echo "php_value error_log /dev/stderr" >> /etc/apache2/apache2.conf

# Discover packages
php artisan package:discover --ansi || true

# Storage link
php artisan storage:link || true

# Clear any stale cache to ensure dynamic environment variables are read
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Ensure correct permissions for www-data
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

exec "$@"
