FROM php:8.2-fpm
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer
