#!/bin/bash
set -e

# Support Render custom port or default to 80
PORT="${PORT:-80}"
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Log PHP errors to stderr so they show up in Render Logs
echo "php_flag display_errors Off" >> /etc/apache2/apache2.conf
echo "php_value error_log /dev/stderr" >> /etc/apache2/apache2.conf

# Create all necessary storage and cache directories if missing
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/testing \
         /var/www/html/storage/app/public \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Mark application as installed
touch /var/www/html/storage/installed

# Clean stale bootstrap cache files from development before discovering
rm -f /var/www/html/bootstrap/cache/*.php

# Discover packages freshly from installed vendor
php artisan package:discover --ansi || true

# Storage link
php artisan storage:link || true

# Clear any stale caches
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Ensure correct permissions for www-data
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

exec "$@"
