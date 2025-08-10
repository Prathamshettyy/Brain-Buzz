# Use an official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies needed for PHP extensions and Composer
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Set the working directory
WORKDIR /var/www/html

# --- Install Composer ---
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- Install PHP dependencies ---
# Copy only composer files first to leverage Docker cache
COPY composer.json composer.lock ./
# Run composer install to download dependencies into the vendor directory
RUN composer install --no-dev --optimize-autoloader

# --- Copy the rest of your application code ---
COPY . .

# Set the correct permissions for the web server
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 and start Apache
EXPOSE 80