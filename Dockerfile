# Use official PHP Apache image
FROM php:8.2-apache

# Install PDO extensions for MySQL and PostgreSQL
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite

# Copy source code into container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose port 80 and start Apache
EXPOSE 80
CMD ["apache2-foreground"]
