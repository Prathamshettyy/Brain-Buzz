# Use official PHP Apache image
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && \
    apt-get install -y libpq-dev libzip-dev zip unzip && \
    # FIX: Added 'mysqli' to the list of extensions to install
    docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql && \
    a2enmod rewrite && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy source code into container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose port 80 and start Apache
EXPOSE 80
CMD ["apache2-foreground"]