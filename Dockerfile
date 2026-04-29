FROM php:8.3-cli

# Install system + Node dependencies
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

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 🔥 Fresh frontend build
RUN rm -rf node_modules package-lock.json
RUN npm install
RUN npm run build

# Expose port
EXPOSE 10000

# 🔥 FINAL runtime (fixes your login issue)
CMD rm -f bootstrap/cache/config.php \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan config:cache \
    && php artisan view:clear \
    && php artisan route:clear \
    && php artisan migrate --force \
    && php artisan db:seed --force || true \
    && php -S 0.0.0.0:$PORT -t public