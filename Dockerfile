FROM php:7-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev mysql-client \
    && docker-php-ext-install mcrypt pdo_mysql
RUN apt-get install -y nginx
ADD . /var/www
ADD ./vhost.conf /etc/nginx/sites-enabled/default
COPY config/php.ini /usr/local/etc/php/
EXPOSE 80
WORKDIR /var/www