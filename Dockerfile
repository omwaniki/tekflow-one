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

# 🔥 Ensure fresh frontend build
RUN rm -rf node_modules package-lock.json
RUN npm install
RUN npm run build

# Expose port
EXPOSE 10000

# 🔥 Stable runtime startup
CMD rm -f bootstrap/cache/config.php \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan view:clear \
    && php artisan route:clear \
    && php artisan migrate --force || true \
    && php -S 0.0.0.0:$PORT -t public