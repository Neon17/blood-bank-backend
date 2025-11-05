#!/bin/sh

echo "==== Starting Laravel container ===="

# To create sessions table if not present
php artisan session:table

echo "Copying env.example to .env"
if [ ! -f .env ]; then
  cp .env.example .env
fi

# Show database configuration
echo "Database configuration:"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_DATABASE: $DB_DATABASE"

# Simple connection test
echo "Testing database connection..."
sleep 3  # Give database time to be ready

# Show Laravel version and app info
echo "Laravel version: $(php artisan --version)"
echo "APP_ENV: $APP_ENV"
echo "APP_DEBUG: $APP_DEBUG"
echo "APP_KEY: ${APP_KEY:-NOT SET}"

echo "🔍 Checking migration status..."

php artisan migrate:fresh --force

php artisan migrate:status
if php artisan migrate:status 2>&1 | grep -q "Pending"; then
    echo "⚠️ Pending migrations found. Running migrate..."
    php artisan migrate --force
elif php artisan migrate:status 2>&1 | grep -q "does not exist"; then
    echo "❗ Migrations table missing. Running migrations for the first time..."
    php artisan migrate --force
elif php artisan migrate:status 2>&1 | grep -q "SQLSTATE"; then
    echo "❌ Database connection issue. Exiting."
    exit 1
else
    echo "✅ All migrations are up to date. Skipping."
fi

# Clear and cache configuration
echo "Clearing and caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Checking if we need to seed data..."
if php artisan tinker --execute="echo User::count();" 2>/dev/null | grep -q "0"; then
    echo "Database is empty, running seeders..."
    php artisan db:seed --force
fi

echo "✅ Database setup complete!"

# FIXED: Use Render's $PORT environment variable
echo "Starting Laravel development server on 0.0.0.0:${PORT}"
exec php artisan serve --host=0.0.0.0 --port=$PORT