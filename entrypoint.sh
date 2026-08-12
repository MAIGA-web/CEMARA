#!/bin/sh

# Attendre et vider les caches de configuration Laravel
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Exécuter les migrations en production
php artisan migrate --force

# Lancer la commande principale du conteneur
exec "$@"
