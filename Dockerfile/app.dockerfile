FROM php:8.1-cli

RUN apt update && apt install -y git zip unzip libzip-dev

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN docker-php-source extract \
	&& docker-php-ext-install zip \
	&& docker-php-source delete

WORKDIR /app
COPY . ./

RUN composer install -o

CMD php bin/app.php