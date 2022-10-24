# syntax=docker/dockerfile:1
FROM php:8.0-cli-alpine

RUN docker-php-ext-install pdo_mysql

COPY test-task/ /

# final configuration
EXPOSE 8000
CMD /