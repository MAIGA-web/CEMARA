FROM php:8.3-apache

# Active le module rewrite Apache
RUN a2enmod rewrite headers
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
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath mbstring gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Installation des dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# CORRECTION 1 : Création explicite de TOUS les sous-dossiers de storage (y compris app/public)
RUN mkdir -p storage/app/public \
             storage/framework/sessions \
             storage/framework/views \
             storage/framework/cache \
             storage/logs \
             bootstrap/cache

# CORRECTION 2 : Droits globaux sur storage et bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 10000

# CORRECTION 3 : Nettoyage de l'ancien lien s'il existe et recréation propre au démarrage
RUN printf '#!/bin/sh\n\
rm -rf /var/www/html/public/storage\n\
php artisan config:clear\n\
php artisan cache:clear\n\
php artisan view:clear\n\
php artisan route:clear\n\
php artisan storage:link --force\n\
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public\n\
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache\n\
exec apache2-foreground\n' > /usr/local/bin/docker-run.sh \
    && chmod +x /usr/local/bin/docker-run.sh

CMD ["/usr/local/bin/docker-run.sh"]
