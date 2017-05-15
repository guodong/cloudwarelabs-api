FROM php:7-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev mysql-client \
    && docker-php-ext-install mcrypt pdo_mysql
RUN apt-get install -y nginx
ADD . /var/www
ADD ./vhost.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
WORKDIR /var/www