#!/bin/bash

echo "==== Production Startup Script ===="

PORT=${PORT:-8000}

# 1. Basic env setup
if [ ! -f .env ]; then
    cp .env.example .env
fi

# 2. Quick health check
echo "Database connection check..."
php artisan tinker --execute="DB::connection()->getPdo()" || {
    echo "❌ Database connection failed"
    exit 1
}

# 3. Run migrations
echo "Running migrations..."
php artisan migrate:fresh --force

# 4. START SERVER FIRST (critical for Render port detection)
echo "🚀 Starting server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT &

# 5. Run non-critical operations in background
(
    echo "Running background tasks..."
    
    # Seed only if empty
    if php artisan tinker --execute="echo User::count()" 2>/dev/null | grep -q "^0$"; then
        echo "Seeding database..."
        php artisan db:seed --force
    fi
    
    # Cache routes and config
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    echo "✅ Background tasks completed"
) &

# 6. Keep the main process alive
wait