FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# 🔥 IMPORTANT: DO NOT CACHE CONFIG HERE

# Expose port
EXPOSE 10000

# 🔥 FINAL FIX: force fresh config + run app
CMD rm -f bootstrap/cache/config.php \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan migrate --force \
    && php -S 0.0.0.0:$PORT -t public