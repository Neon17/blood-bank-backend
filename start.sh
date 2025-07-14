#!/bin/sh

echo "==== Starting Laravel container ===="

# To create sessions table if not present
php artisan session:table

# Copy .env.example to .env if not present
echo "Copying env.example to .env"
if [ ! -f .env ]; then
  cp .env.example .env
fi

# Replace placeholders using actual environment vars
# envsubst < .env.example > .env

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

# Show database configuration
echo "Laravel sees this DB config:"
php artisan tinker --execute="dump(config('database.connections.pgsql'))"

echo "🔍 Checking migration status..."
echo "php artisan migrate:status = ".$(php artisan migrate:status);
php artisan migrate --force
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

# Optional: Run seeders only if tables are empty (uncomment if needed)
# if [ "$APP_ENV" = "production" ]; then
#   echo "Checking if we need to seed data..."
#   if php artisan tinker --execute="echo User::count();" 2>/dev/null | grep -q "0"; then
#     echo "Database is empty, running seeders..."
#     php artisan db:seed --force
#   fi
# fi

echo "✅ Database setup complete!"

# Start Laravel app
echo "Starting Laravel development server on 0.0.0.0:8000"
exec php artisan serve --host=0.0.0.0 --port=8000