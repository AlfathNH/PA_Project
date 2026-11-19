#!/bin/bash
set -e

# Render dynamic port binding
PORT="${PORT:-80}"
echo "[Deploy] Binding Apache to port $PORT..."
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Fallback APP_KEY if not specified
if [ -z "$APP_KEY" ]; then
    echo "[Deploy] Setting default APP_KEY..."
    export APP_KEY="base64:ueJta0o1+95dE+NEeDx/9rIl9jGCnlrx7EIkaK/LeLQ="
fi

# Setup SQLite if using sqlite or default
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    echo "[Deploy] Initializing SQLite database..."
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
    chmod -R 775 /var/www/html/database

    echo "[Deploy] Running migrations and seeders..."
    php artisan migrate:fresh --seed --force
else
    echo "[Deploy] Running migrations..."
    php artisan migrate --force
fi

# Optimization caches
echo "[Deploy] Optimizing caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "[Deploy] Application is ready! Starting Apache..."
exec apache2-foreground
