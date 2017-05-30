FROM eboraas/laravel

ADD . /var/www/laravel
RUN chmod -R 777 /var/www/laravel/storage && chmod -R 777 /var/www/laravel/bootstrap/cache
EXPOSE 80
WORKDIR /var/www/laravel
RUN apt-get update
RUN apt-get install -y php5-curl