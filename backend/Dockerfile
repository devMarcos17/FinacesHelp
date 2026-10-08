FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    sqlite-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

EXPOSE 8000

ENTRYPOINT []
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]