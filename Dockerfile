FROM eboraas/laravel

ADD . /var/www/laravel
EXPOSE 80
WORKDIR /var/www