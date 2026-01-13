#!/bin/bash
set -e

echo "==> Starting application..."

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Clear and cache config
echo "==> Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "==> Running migrations..."
php artisan migrate --force || echo "Migration failed, continuing..."

echo "==> Seeding database..."
php artisan db:seed --force || echo "Seeding skipped (already seeded)"

echo "==> Starting server on port 10000..."
exec php artisan serve --host=0.0.0.0 --port=10000
