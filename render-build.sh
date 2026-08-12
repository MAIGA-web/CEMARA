cat << 'EOF' > render-build.sh
#!/usr/bin/env bash
set -o errexit

# Installation des dépendances Composer sans les packages de dev
composer install --no-dev --optimize-autoloader

# Build des assets front-end si vous utilisez NPM (facultatif si déjà compilé)
if [ -f package.json ]; then
    npm install && npm run build
fi

# Optimisation du cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Exécution des migrations PostgreSQL
php artisan migrate --force
EOF