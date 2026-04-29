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

# Clear Laravel caches
RUN php artisan config:clear && php artisan cache:clear

# Expose port
EXPOSE 10000

# ✅ SAFE startup (will not crash container)
CMD php artisan migrate --force || true \
    && php -S 0.0.0.0:$PORT -t public