FROM php:8.2-cli

# Installation des dépendances système et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath mbstring gd

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /var/www/html

# Copie du projet
COPY . .

# Installation des dépendances Composer en ignorant temporairement les règles de plateforme strictes
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Gestion des permissions pour Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Port d'écoute Render
EXPOSE 10000

# Commande d'exécution : exécute les migrations puis démarre le serveur web
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
