FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
     git \
     unzip \
     libzip-dev \
     && docker-php-ext-install zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

COPY . .

RUN composer install --ignore-platform-req=ext-yaml

EXPOSE 1337

CMD ["php", "src/main.php"]
