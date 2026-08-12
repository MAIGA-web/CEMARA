FROM php:8.3-apache

# Active le module rewrite Apache
RUN a2enmod rewrite

# DocumentRoot pour Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Changement du port pour Render (10000)
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Installation des dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath mbstring gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Installation des dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# Création explicite des sous-dossiers de storage
RUN mkdir -p storage/framework/sessions \
             storage/framework/views \
             storage/framework/cache \
             storage/logs \
             bootstrap/cache

# Configuration des permissions
RUN chmod -R 777 storage bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 10000

# Script d'exécution au démarrage du conteneur
RUN printf '#!/bin/sh\n\
php artisan config:clear\n\
php artisan cache:clear\n\
php artisan view:clear\n\
php artisan route:clear\n\
php artisan migrate --force || true\n\
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache\n\
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache\n\
exec apache2-foreground\n' > /usr/local/bin/docker-run.sh \
    && chmod +x /usr/local/bin/docker-run.sh

CMD ["/usr/local/bin/docker-run.sh"]