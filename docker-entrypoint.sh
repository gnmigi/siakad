#!/bin/bash
set -e

# Support Render custom port or default to 80
PORT="${PORT:-80}"
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Discover packages now that environment variables are loaded
php artisan package:discover --ansi || true

# Storage link
php artisan storage:link || true

# Optimize cache if APP_KEY is provided
if [ -n "$APP_KEY" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Run database migration if configured
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force || true
fi

exec "$@"
