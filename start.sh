#!/bin/sh

echo "==== Starting Laravel container ===="

php artisan config:clear
php artisan config:cache

echo "Laravel version:"
php artisan --version

echo "APP_ENV: $APP_ENV"
echo "APP_DEBUG: $APP_DEBUG"
echo "APP_KEY: ${APP_KEY:-NOT SET}"

echo "Running migrations..."
if php artisan migrate --force; then
  echo "Migrations ran successfully."
else
  echo "Migration failed! Exiting."
  exit 1
fi

echo "Starting Laravel development server on 0.0.0.0:8000"
php artisan serve --host=0.0.0.0 --port=8000
