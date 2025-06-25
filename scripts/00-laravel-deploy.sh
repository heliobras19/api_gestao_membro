
#!/usr/bin/env bash
# scripts/00-laravel-deploy.sh

echo "Running Laravel deployment script..."

# Gerar APP_KEY se não existir
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --no-interaction --force
fi

# Executar migrações
echo "Running migrations..."
php artisan migrate --no-interaction --force

# Cache das configurações
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpar cache antigo
echo "Clearing old cache..."
php artisan cache:clear

echo "Laravel deployment completed!"