FROM dunglas/frankenphp:php8.4

RUN install-php-extensions mysqli

WORKDIR /app

ENV SERVER_NAME=:8080

COPY . /app