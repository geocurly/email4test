FROM php:8.1-fpm

RUN apt-get update && apt-get -y install cron \
    libpq-dev && docker-php-ext-install pdo pdo_pgsql pgsql

COPY crontab /etc/cron.d/crontab

RUN /usr/bin/crontab /etc/cron.d/crontab

CMD cron -f && sleep infinity