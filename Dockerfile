FROM composer:2 AS composer
FROM php:7.4-cli AS wordpress-cli

USER root

RUN apt-get update && apt-get install -y libzip-dev zip && \
    mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini && \
    pecl install xdebug-3.1.5 && \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip && \
    docker-php-ext-enable xdebug && \
    echo "[xdebug]" >> /usr/local/etc/php/php.init && \
    echo "xdebug.mode=coverage" >> /usr/local/etc/php/php.ini

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
