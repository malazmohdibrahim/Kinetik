# Use the official PHP 8.2 image with Apache built-in
FROM php:8.2-apache

# Install the PDO MySQL extension so PHP can talk to your database securely
RUN docker-php-ext-install pdo pdo_mysql

# Explicitly tell Apache to handle .php files correctly instead of serving them as downloads
RUN a2enmod rewrite

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy your local PHP application code into the server directory
COPY ./src /var/www/html/

# Expose web server traffic port
EXPOSE 80