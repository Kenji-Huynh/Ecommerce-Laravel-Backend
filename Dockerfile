FROM php:8.3-fpm

# Install OS packages and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    nginx \
    ca-certificates \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring zip gd fileinfo bcmath \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (layer cache)
COPY composer.json composer.lock ./

# Create storage/cache directories before Composer runs
RUN mkdir -p bootstrap/cache \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    && chmod -R 777 storage bootstrap/cache

# Install PHP dependencies (do not run scripts during install)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy application code
COPY . .

# Ensure directories and permissions (again after copy)
RUN mkdir -p bootstrap/cache storage/framework/{cache/data,sessions,views} storage/logs storage/app/public \
    && chmod -R 777 storage bootstrap/cache

# Run Laravel package discovery now that files and cache paths exist
RUN php artisan package:discover --ansi || true

# Copy nginx config
COPY nginx.conf /etc/nginx/sites-available/default

# Copy entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
