# ---------------------------------------------
# Tahap 1: Kompilasi Aset Frontend (Tailwind / Vite)
# ---------------------------------------------
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci || npm install
COPY . .
RUN npm run build

# ---------------------------------------------
# Tahap 2: Runtime PHP & Eksekusi Laravel
# ---------------------------------------------
FROM php:8.4-cli-alpine

# Install ekstensi PHP & dependensi sistem yang dibutuhkan
RUN apk add --no-cache \
    curl \
    git \
    bash \
    zip \
    unzip \
    libpng-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql bcmath pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin source code
COPY . .

# Salin aset frontend hasil kompilasi dari Tahap 1
COPY --from=frontend /app/public/build ./public/build

# Install dependensi PHP tanpa dev dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Berikan hak akses storage dan bootstrap
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]

