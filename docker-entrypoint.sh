#!/usr/bin/env bash
set -e

# Ensure runtime directories exist and are writable
mkdir -p bootstrap/cache \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  storage/app/public
chmod -R 777 storage bootstrap/cache || true

# Generate app key if missing (non-interactive)
if [ -z "$(php artisan key:generate --show 2>/dev/null || true)" ]; then
  php artisan key:generate --force || true
fi

# Run migrations if requested
if [ "${RUN_MIGRATIONS,,}" = "true" ] || [ "${RUN_MIGRATIONS}" = "1" ]; then
  echo "Running migrations..."
  php artisan migrate --force || true
fi

# Run db seed if requested
if [ "${RUN_SEED,,}" = "true" ] || [ "${RUN_SEED}" = "1" ]; then
  echo "Running db:seed..."
  php artisan db:seed --force || true
fi

# Start php-fpm in background and nginx in foreground
php-fpm --nodaemonize &
nginx -g 'daemon off;'
#!/bin/sh
set -e

echo "🚀 Starting Laravel application..."

# Run migrations with fresh reset
echo "📦 Running database migrations..."
php artisan migrate:fresh --force || php artisan migrate --force

# Seed database (optional - comment out if you don't want to reseed every time)
if [ "$APP_ENV" = "production" ] && [ ! -f /app/storage/.seeded ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
    touch /app/storage/.seeded
fi

# Start Laravel server
echo "✅ Starting server..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
