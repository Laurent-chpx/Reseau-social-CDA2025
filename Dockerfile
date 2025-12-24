FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libsodium-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip sodium

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html


