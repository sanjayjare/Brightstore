FROM skiychan/nginx-php7

Expose 80
Expose 443

copy .src /data/www/

