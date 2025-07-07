# Use official PHP image with FPM
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring bcmath gd zip

# Set working directory
WORKDIR /var/www

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel project files into the container
COPY . .

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader

# Set correct permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Expose port Laravel will run on
EXPOSE 8000

# Run migrations and start Laravel server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
