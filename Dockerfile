FROM php:8.1-fpm
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN apt update && \
    apt install -y $PHPIZE_DEPS git unzip cron nano && \
    docker-php-ext-install pdo pdo_mysql mysqli
RUN mkdir -p /app
#RUN crontab -l | { cat; echo "0 15 * * * php /app/cron.php"; } | crontab -
RUN apt-get update && apt-get install -y cron && which cron && \
    rm -rf /etc/cron.*/*

WORKDIR /app

#COPY --chmod=777 ./entrypoint.sh /entrypoint.sh

#ENTRYPOINT ["/entrypoint.sh"]
CMD ["cron","-f", "-l", "2"]
