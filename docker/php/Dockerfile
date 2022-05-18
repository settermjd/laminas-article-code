ARG FPM_VERSION=alpine3.15
ARG PHP_VERSION=7.4.29

FROM php:${PHP_VERSION}-fpm-${FPM_VERSION} as base

FROM base as core

ARG ENV

RUN if [ "$ENV" = "development" ]; then \
    apk --update-cache add autoconf gcc musl-dev make && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*; \
fi

RUN apk --update-cache add git libzip-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-enable zip \
    && rm -rf /var/lib/apt/lists/*

FROM base as staging

WORKDIR /var/www/html

COPY . .

RUN --mount=from=composer:2.2.7,source=/usr/bin/composer,target=/usr/bin/composer \
    composer install \
    --no-dev --no-ansi --no-plugins --no-progress --no-scripts \
    --classmap-authoritative --no-interaction \
    --quiet
