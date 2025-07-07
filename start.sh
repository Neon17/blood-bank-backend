#!/bin/sh

# Generate app key if not set (safe to run multiple times)
# php artisan key:generate

#!/bin/sh

echo "APP_ENV=$APP_ENV"
echo "APP_DEBUG=$APP_DEBUG"
echo "APP_KEY=${APP_KEY:-NOT SET}"

php artisan migrate --force || { echo "Migration failed"; exit 1; }

php artisan serve --host=0.0.0.0 --port=8000

