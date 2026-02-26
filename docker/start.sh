#!/bin/sh

# Set correct permissions
chown -R www-data:www-data /var/www
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Run Cache clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
# Force is used to run migrations in production
touch /var/www/database/database.sqlite
php artisan migrate --force

# Start Supervisor (which starts Nginx, PHP-FPM, and Queue Worker)
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
