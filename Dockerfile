FROM php:8.2-apache

RUN apt-get update \
    && docker-php-ext-install mysqli \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html

RUN mkdir -p /var/www/html/assets/img \
    && chown -R www-data:www-data /var/www/html/assets/img

ENV APACHE_DOCUMENT_ROOT=/var/www/html

EXPOSE 80
