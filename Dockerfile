FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 🔥 Install frontend dependencies + build assets
RUN npm install && npm run build

# Expose port
EXPOSE 10000

# Start app
CMD php artisan config:clear \
    && php artisan cache:clear \
    && php artisan migrate --force || true \
    && php -S 0.0.0.0:$PORT -t public