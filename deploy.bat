@echo off
echo === Glow & Charm Station - Hostinger Deployment ===
echo.

echo Step 1: Upload deploy.tar.gz to your Hostinger home directory
echo   Use FTP/SFTP (FileZilla, Cyberduck) to upload deploy.tar.gz to /home/your_username/
echo.
echo Step 2: SSH into Hostinger and run:
echo   tar -xzf deploy.tar.gz -C /home/your_username/laravel/
echo   cd /home/your_username/laravel/
echo   composer install --optimize-autoloader --no-dev --no-interaction
echo   php artisan key:generate --force
echo   php artisan migrate --force
echo   php artisan storage:link
echo   php artisan config:cache
echo   php artisan route:cache
echo   php artisan view:cache
echo   chmod -R 755 .
echo   chmod -R 775 storage/ bootstrap/cache/
echo   cp -r ../public_html ../public_html_backup 2>nul
echo   cp public/* ../public_html/
echo   cp public/.htaccess ../public_html/
echo.
echo Step 3: Edit ../laravel/.env with Hostinger database credentials
echo Step 4: Set up cron job in hPanel (every 5 min):
echo   php /home/your_username/laravel/artisan schedule:run >> /dev/null 2>&1
echo.
echo === Done! Visit https://your-domain.com ===
