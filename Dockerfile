FROM php:8.1-fpm-alpine
RUN apk add oniguruma-dev libzip-dev libpq-dev git openssh busybox-extras autoconf gcc g++ make
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip exif pcntl
RUN pecl install -o -f redis
RUN docker-php-ext-enable redis
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
