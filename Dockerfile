FROM php:8.2-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y libsqlite3-dev && docker-php-ext-install pdo pdo_sqlite

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache virtual host with public/ as document root and .htaccess support
RUN { \
    echo '<VirtualHost *:80>'; \
    echo '    DocumentRoot /var/www/html/public'; \
    echo '    <Directory /var/www/html/public>'; \
    echo '        AllowOverride All'; \
    echo '        Require all granted'; \
    echo '    </Directory>'; \
    echo '</VirtualHost>'; \
} > /etc/apache2/sites-available/000-default.conf

# Production environment defaults (can be overridden at runtime via -e or docker-compose environment)
ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_URL=http://localhost \
    DB_DRIVER=sqlite

# Copy application files
COPY . /var/www/html/

# Set proper ownership and permissions for writable directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/public/uploads \
    && chmod -R 755 /var/www/html/storage /var/www/html/public/uploads

# Create entrypoint script for first-run conditional migration
RUN { \
    echo '#!/bin/sh'; \
    echo 'set -e'; \
    echo ''; \
    echo '# Ensure storage directories exist (db, sessions)'; \
    echo 'mkdir -p /var/www/html/storage/db /var/www/html/storage/sessions'; \
    echo ''; \
    echo '# Fix ownership on mounted volumes (NAS mount does not inherit image permissions)'; \
    echo 'chown -R www-data:www-data /var/www/html/storage/sessions /var/www/html/storage/db'; \
    echo ''; \
    echo '# Run migration only if the database does not exist yet'; \
    echo 'if [ ! -f /var/www/html/storage/db/app.db ]; then'; \
    echo '    echo "*** First start: running database migration with seeder... ***"'; \
    echo '    php /var/www/html/database/migrate.php --seed'; \
    echo '    echo "*** Migration complete ***"'; \
    echo 'fi'; \
    echo ''; \
    echo '# Start Apache in foreground'; \
    echo 'exec apache2-foreground'; \
} > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Set working directory
WORKDIR /var/www/html

# Expose port
EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
