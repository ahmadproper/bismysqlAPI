FROM php:7.2-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev mariadb-client libxml2-dev \
    && pecl install mcrypt-1.0.2 \
    && docker-php-ext-enable mcrypt \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install -j$(nproc) \
            dom \
            xmlrpc

WORKDIR /var/www
ADD . /var/www
RUN chown -R www-data:www-data /var/www
