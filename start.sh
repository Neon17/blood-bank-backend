#!/bin/bash

echo "==== Production Startup Script ===="

PORT=${PORT:-8000}

# 1. Basic env setup
if [ ! -f .env ]; then
    cp .env.example .env
fi

# 2. Clear any stale config cache so env overrides take effect
php artisan config:clear 2>/dev/null

# 3. Check if PostgreSQL is reachable; fall back to SQLite if not
if [ -n "$DB_HOST" ]; then
    echo "Checking PostgreSQL at $DB_HOST:${DB_PORT:-5432}..."
    if (timeout 5 bash -c "echo > /dev/tcp/$DB_HOST/${DB_PORT:-5432}") 2>/dev/null; then
        echo "PostgreSQL reachable, using pgsql"
    else
        echo "PostgreSQL unreachable, falling back to SQLite"
        export DB_CONNECTION=sqlite
        export DB_DATABASE=/var/www/database/database.sqlite
        touch /var/www/database/database.sqlite
    fi
else
    echo "No DB_HOST configured, using SQLite"
    export DB_CONNECTION=sqlite
    export DB_DATABASE=/var/www/database/database.sqlite
    touch /var/www/database/database.sqlite
fi

# 4. Run migrations
echo "Running migrations..."
php artisan migrate --force

# 5. START SERVER FIRST (critical for Render port detection)
echo "Starting server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT &

# 6. Run non-critical operations in background
(
    echo "Running background tasks..."

    # Seed only if empty
    if php artisan tinker --execute="echo User::count()" 2>/dev/null | grep -q "^0$"; then
        echo "Seeding database..."
        php artisan db:seed --force
    fi

    # Cache config/routes/views (captures current DB_CONNECTION so it stays consistent)
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "Background tasks completed"
) &

# 7. Keep the main process alive
wait
