#!/bin/sh

# Generate app key if not set (safe to run multiple times)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Start Laravel dev server
php artisan serve --host=0.0.0.0 --port=8000
