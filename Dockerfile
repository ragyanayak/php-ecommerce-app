FROM php:8.3-apache

WORKDIR /var/www/html

COPY index.php /var/www/html/
COPY cart.php /var/www/html/
COPY style.css /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
