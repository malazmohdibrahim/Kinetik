FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

# This forces Apache to stop looking for the conflicting module files
RUN echo "LoadModule mpm_prefork_module /usr/lib/apache2/modules/mod_mpm_prefork.so" > /etc/apache2/mods-enabled/mpm_prefork.load && \
    rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_worker.load && \
    a2enmod rewrite

WORKDIR /var/www/html
COPY ./src /var/www/html/

EXPOSE 80