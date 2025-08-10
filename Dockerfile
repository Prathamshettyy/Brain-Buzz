# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache

# Install system dependencies required for PHP extensions and Composer
# ADDED: libpq-dev is required for the PostgreSQL driver
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install the required PHP extensions using the official Docker helper script
# ADDED: pdo_pgsql is the driver for PostgreSQL
RUN docker-php-ext-install zip pdo pdo_mysql pdo_pgsql

# Set the working directory for the application
WORKDIR /var/www/html

# --- Install Composer (PHP package manager) ---
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- Install PHP dependencies ---
# Copy only composer files first to leverage Docker's build cache
COPY composer.json composer.lock ./
# Run composer install to download dependencies into the vendor/ directory
RUN composer install --no-dev --optimize-autoloader

# --- Copy the rest of your application code ---
COPY . .

# Set the correct permissions for the web server to write logs and files
RUN chown -R www-data:www-data /var/www/html

# Enable Apache's mod_rewrite for clean URLs (like Laravel, WordPress, etc.)
RUN a2enmod rewrite

# Expose port 80 and start the Apache web server
EXPOSE 80