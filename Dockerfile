FROM vernonco/nginx-php-fpm:php7

Expose 80
Expose 443

copy ./src /var/www/html

