FROM php:8.2.2-fpm-alpine

ENV RUN_DEPS \
    zlib \
    libzip \
    libpng \
    libjpeg-turbo \
    postgresql-libs

ENV BUILD_DEPS \
    zlib-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    postgresql-dev

ENV PHP_EXTENSIONS \
    opcache \
    zip \
    gd \
    bcmath \
    pdo \
    pgsql \
    pdo_pgsql

RUN apk add --no-cache --update linux-headers \
    && apk --no-cache --virtual .build-deps add pcre-dev ${PHPIZE_DEPS} \
    && pecl install xdebug \
    && apk add --no-cache --virtual .build-deps $BUILD_DEPS \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) $PHP_EXTENSIONS \
    && docker-php-ext-enable xdebug pdo_pgsql pgsql \
    && apk del .build-deps

RUN apk add --no-cache --virtual .run-deps $RUN_DEPS

COPY ./docker/php/conf.d/ /usr/local/etc/php/conf.d/

COPY . /app/

VOLUME ["/app"]