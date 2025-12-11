FROM php:8.4-fpm-alpine

RUN apk update && apk add --no-cache \
    bash \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libxml2-dev \
    oniguruma-dev \
    mysql-client \
    mariadb-connector-c-dev \
    mariadb-dev \
    build-base \
    autoconf \
    && docker-php-ext-install pdo_mysql mysqli gd

WORKDIR /var/www/html
