FROM php:8.2-apache

# Install the PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# FIX: Disable the default conflicting MPM and enable rewrite
# This keeps your MySQL connection logic fully intact
RUN a2dismod mpm_event && a2enmod mpm_prefork && a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy your local PHP application code
COPY ./src /var/www/html/

# Expose web server traffic port
EXPOSE 80