FROM php:8.1-fpm
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN apt update && \
    apt install -y $PHPIZE_DEPS git unzip && \
    docker-php-ext-install pdo pdo_mysql mysqli
RUN mkdir -p /app
WORKDIR /app
