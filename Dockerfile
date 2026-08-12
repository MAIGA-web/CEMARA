cat << 'EOF' > Dockerfile
FROM php:8.2-fpm

# Installation des dépendances système et des extensions PHP (pdo_pgsql)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    nginx \
    && docker-php-ext-install pdo pdo_pgsql

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie du code source
WORKDIR /var/www/html
COPY . .

# Installation des dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Commande de lancement
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
EOF