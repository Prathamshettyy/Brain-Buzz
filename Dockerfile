# Use the official PHP 8.2 image with Apache for a stable foundation
FROM php:8.2-apache

# Set the working directory for the application inside the container
WORKDIR /var/www/html

# Install essential system libraries required for PHP extensions and Composer.
# libpq-dev is for PostgreSQL, libzip-dev is for zip support, and unzip is for Composer.
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install the required PHP extensions using the official Docker helper script.
# This ensures PHP can connect to your databases and handle zip files.
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# --- Dependency Installation ---
# This is the most critical part.

# 1. Install Composer (the PHP package manager) globally in the container.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 2. Copy only the dependency list files. This is a caching optimization.
#    Docker will only re-run the next step if these specific files change.
COPY composer.json composer.lock ./

# 3. Run composer install. This reads the composer.json file and downloads
#    all required libraries (like PHPMailer) into a 'vendor' directory.
#    --no-dev skips development-only packages to keep the image small.
#    --optimize-autoloader creates a faster autoloader for production.
RUN composer install --no-dev --optimize-autoloader

# --- Finalizing the Application ---

# 4. Now that dependencies are installed, copy the rest of your application code.
COPY . .

# 5. Set the correct file permissions. The Apache web server runs as the
#    'www-data' user and needs ownership to write logs or handle file uploads.
RUN chown -R www-data:www-data /var/www/html

# 6. Enable Apache's mod_rewrite. This is necessary for clean URLs, which
#    are used by most modern frameworks and applications.
RUN a2enmod rewrite

# 7. Expose port 80 to the outside world and start the Apache server.
EXPOSE 80