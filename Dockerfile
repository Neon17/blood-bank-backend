FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libzip-dev libonig-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath gd zip

# Set working directory
WORKDIR /var/www

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 8000

# Start Laravel's development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
