#!/bin/sh

echo "==== Starting Laravel container ===="

# Show database configuration
echo "Database configuration:"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_DATABASE: $DB_DATABASE"

# Simple connection test
echo "Testing database connection..."
sleep 3  # Give database time to be ready

# Clear and cache configuration
echo "Clearing and caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Show Laravel version and app info
echo "Laravel version: $(php artisan --version)"
echo "APP_ENV: $APP_ENV"
echo "APP_DEBUG: $APP_DEBUG"
echo "APP_KEY: ${APP_KEY:-NOT SET}"

# Run migrations (this will test the real connection)
echo "Running migrations..."
if php artisan migrate --force; then
  echo "✅ Migrations ran successfully."
else
  echo "❌ Migration failed! Check database connection."
  echo "Current DB_CONNECTION: $DB_CONNECTION"
  exit 1
fi

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