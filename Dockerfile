FROM php:8.2-fpm

RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo_mysql
# for mysqli if you want
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

CMD ["php-fpm"]