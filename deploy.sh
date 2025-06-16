#!/bin/bash

echo "🔄 Pulling latest changes from Git..."
git pull origin main

echo "📦 Installing/updating dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "🔧 Running Laravel post-deploy commands..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache
php artisan storage:link

echo "🔐 Fixing file permissions..."
sudo chown -R www-data:www-data .
sudo chown -R www-data:www-data storage bootstrap/cache

echo "✅ Deployment complete!"
