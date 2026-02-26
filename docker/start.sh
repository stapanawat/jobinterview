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
chown -R www-data:www-data /var/www/database
chmod -R 775 /var/www/database
chmod 664 /var/www/database/database.sqlite
php artisan migrate --force

# Replace the $PORT environment variable in nginx.conf
envsubst '${PORT}' < /etc/nginx/sites-enabled/default > /etc/nginx/sites-enabled/default.tmp
mv /etc/nginx/sites-enabled/default.tmp /etc/nginx/sites-enabled/default

# Start Supervisor (which starts Nginx, PHP-FPM, and Queue Worker)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
