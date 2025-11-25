#!/bin/sh
set -e

echo "🚀 Starting Laravel Ecommerce Backend..."

# ----------------------------
# 1. Ensure required directories
# ----------------------------
for dir in \
  bootstrap/cache \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  storage/app/public; do
  [ -d "$dir" ] || mkdir -p "$dir"
done

# Permissions (best effort, non-fatal). Use 777 to avoid permission issues in hosted containers.
chmod -R 777 storage bootstrap/cache || true

# ----------------------------
# 2. Package discovery + APP_KEY guard
# ----------------------------
echo "📦 Preparing application..."
if [ -z "${APP_KEY}" ]; then
  echo "🔑 APP_KEY missing – generating one (persist by setting env var next deploy)."
  php artisan key:generate --force || echo "⚠️  Failed to generate key"
fi
php artisan package:discover --ansi || echo "⚠️  Package discovery failed (continuing)"

# ----------------------------
# 3. Cache / optimize (prod) or clear (non-prod)
# ----------------------------
if [ "${APP_ENV}" = "production" ]; then
  echo "⚡ Optimizing (config/route/view cache)..."
  php artisan config:cache || echo "⚠️  Config cache failed"
  php artisan route:cache || echo "⚠️  Route cache failed"
  php artisan view:cache || echo "⚠️  View cache failed"
else
  echo "🧹 Dev environment – clearing caches..."
  php artisan config:clear || true
  php artisan route:clear || true
  php artisan view:clear || true
fi

# ----------------------------
# 4. Migrations / Seeding
# ----------------------------
run_migrations() {
  set +e
  php artisan "$1" --force -vvv
  local EXIT=$?
  set -e
  if [ $EXIT -ne 0 ]; then
    echo "❌ $1 failed (exit $EXIT)"
  else
    echo "✅ $1 completed"
  fi
}

if [ "${DB_FRESH_MIGRATE}" = "true" ]; then
  echo "🗄️  migrate:fresh starting..."
  run_migrations migrate:fresh
elif [ "${RUN_MIGRATIONS_ON_START}" = "true" ]; then
  echo "🗄️  migrate starting..."
  run_migrations migrate
else
  echo "⏭️  Skipping migrations (RUN_MIGRATIONS_ON_START=true to enable)"
fi

if [ "${RUN_SEED_ON_START}" = "true" ]; then
  echo "🌱 Seeding database..."
  php artisan db:seed --force || echo "⚠️  Seeding failed"
else
  echo "⏭️  Skipping seeding (RUN_SEED_ON_START=true to enable)"
fi

# ----------------------------
# 5. Start Server (Railway sets $PORT)
# ----------------------------
PORT=${PORT:-8080}
echo "✅ Server listening on 0.0.0.0:${PORT}"
exec php artisan serve --host=0.0.0.0 --port=${PORT}
