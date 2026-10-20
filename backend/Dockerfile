FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    git curl libpng-dev oniguruma-dev libxml2-dev \
    postgresql-dev sqlite-dev zip unzip \
    && docker-php-ext-install pdo_pgsql pdo_sqlite mbstring exif pcntl bcmath gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . /var/www

RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 10000

ENTRYPOINT []
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]