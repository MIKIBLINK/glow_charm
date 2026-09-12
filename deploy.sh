#!/bin/bash
set -e

echo "=== Glow & Charm Station - Hostinger Deployment ==="
echo ""

LARAVEL_DIR="/home/$(whoami)/laravel"
PUBLIC_HTML_DIR="/home/$(whoami)/public_html"

echo "Step 1: Extract project"
mkdir -p "$LARAVEL_DIR"
tar -xzf deploy.tar.gz -C "$LARAVEL_DIR" --strip-components=1
rm -f deploy.tar.gz
echo "  Extracted to $LARAVEL_DIR"

echo "Step 2: Set up web root (public_html)"
cp -r "$LARAVEL_DIR/public"/* "$PUBLIC_HTML_DIR/"
cp -f "$LARAVEL_DIR/public"/.htaccess "$PUBLIC_HTML_DIR/" 2>/dev/null || true
echo "  Public assets copied to $PUBLIC_HTML_DIR"

echo "Step 3: Fix index.php paths"
cat > "$PUBLIC_HTML_DIR/index.php" << 'INDEXEOF'
<?php
define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../laravel/vendor/autoload.php';

$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

$app->handleRequest(\Illuminate\Http\Request::capture());
INDEXEOF
echo "  index.php updated"

echo "Step 4: Create .env from production template"
cp "$LARAVEL_DIR/.env.production" "$LARAVEL_DIR/.env"
echo "  .env.production copied to .env"
echo "  EDIT $LARAVEL_DIR/.env with your Hostinger database credentials:"
echo "  - DB_DATABASE, DB_USERNAME, DB_PASSWORD (from hPanel -> Databases -> MySQL)"
echo "  - APP_URL=https://your-domain.com"

echo "Step 5: Install Composer dependencies"
cd "$LARAVEL_DIR"
composer install --optimize-autoloader --no-dev --no-interaction

echo "Step 6: Generate app key"
php artisan key:generate --force

echo "Step 7: Run migrations"
php artisan migrate --force

echo "Step 8: Create storage symlink"
php artisan storage:link

echo "Step 9: Cache optimization"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Step 10: Set permissions"
cd /home/$(whoami)
chmod -R 755 laravel/
chmod -R 775 laravel/storage/
chmod -R 775 laravel/bootstrap/cache/
chmod -R 755 public_html/

echo ""
echo "=== Deployment Complete! ==="
echo ""
echo "1. Edit $LARAVEL_DIR/.env BEFORE running Step 6-7 if you haven't already"
echo "2. Register first user at https://your-domain.com/register (becomes admin)"
echo "3. Cron job (hPanel -> Advanced -> Cron Jobs, every 5 min):"
echo "  php $LARAVEL_DIR/artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "=== Done! ==="
