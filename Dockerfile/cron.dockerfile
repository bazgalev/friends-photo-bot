FROM php:8.1-cli

RUN apt update && apt -y install git zip unzip cron libzip-dev

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN docker-php-source extract \
	&& docker-php-ext-install zip \
	&& docker-php-source delete

WORKDIR /app
COPY . ./

ADD crontab /etc/cron.d/cron-app
RUN chmod 0644 /etc/cron.d/cron-app
RUN crontab /etc/cron.d/cron-app
RUN touch /var/log/cron.log

RUN composer install -o

CMD cron && tail -f /var/log/cron.log