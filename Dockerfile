# Use the official PHP 8.2 image with Apache built-in
FROM php:8.2-apache

# Install the PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# FIX: Explicitly disable the default event MPM and enable prefork before enabling rewrite
RUN a2dismod mpm_event && \
    a2enmod mpm_prefork && \
    a2enmod rewrite

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy your local PHP application code into the server directory
COPY ./src /var/www/html/

# Expose web server traffic port
EXPOSE 80