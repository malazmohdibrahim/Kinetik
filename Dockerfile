FROM php:8.2-cli

# Install the PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Set the working directory
WORKDIR /var/www/html

# Copy your application code
COPY ./src /var/www/html/

# Use the built-in PHP development server to run your app
# This avoids Apache and its MPM conflicts entirely
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]