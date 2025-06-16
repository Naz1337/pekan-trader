#!/bin/bash

PROJECT_DIR="/var/www/pekan-trader"
PHP_FPM_VERSION="php8.1-fpm"  # Change if you're using a different PHP version

echo "🔄 Pulling latest changes from GitHub..."
sudo -u www-data git -C "$PROJECT_DIR" pull origin main

echo "📦 Installing/updating dependencies via Composer..."
sudo -u www-data COMPOSER_ALLOW_SUPERUSER=1 composer install \
  --no-interaction --prefer-dist --optimize-autoloader --working-dir="$PROJECT_DIR"

echo "🔧 Running Laravel artisan commands..."
sudo -u www-data php "$PROJECT_DIR/artisan" config:clear
sudo -u www-data php "$PROJECT_DIR/artisan" config:cache
sudo -u www-data php "$PROJECT_DIR/artisan" route:clear
sudo -u www-data php "$PROJECT_DIR/artisan" route:cache
sudo -u www-data php "$PROJECT_DIR/artisan" view:clear
sudo -u www-data php "$PROJECT_DIR/artisan" view:cache
sudo -u www-data php "$PROJECT_DIR/artisan" storage:link

echo "🔐 Fixing permissions..."
chown -R www-data:www-data "$PROJECT_DIR"
chown -R www-data:www-data "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"

echo "✅ Deployment complete!"
