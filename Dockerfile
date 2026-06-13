FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

# We do NOT run a2enmod here. 
# We will enable modules via the Start Command if needed, 
# but for now, let's see if this builds clean.
WORKDIR /var/www/html
COPY ./src /var/www/html/

EXPOSE 80