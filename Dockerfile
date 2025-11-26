# Dependencies build stage (Composer)
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress
COPY . .
RUN composer dump-autoload --optimize

# Runtime stage (PHP 8.2 + Apache)
FROM php:8.2-apache

# Enable Apache rewrite for Laravel routes
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Install Postgres PDO extension (the app uses pgsql)
RUN apt-get update && apt-get install -y libpq-dev && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql && \
    docker-php-ext-install pdo pdo_pgsql && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY --chown=www-data:www-data --from=vendor /app /var/www/html

# Ensure Laravel can write cache/logs
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose HTTP port
EXPOSE 80

# Healthcheck (basic)
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s \
  CMD curl -f http://localhost/ || exit 1