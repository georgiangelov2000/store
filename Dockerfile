# Use the latest PHP-FPM image
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/store

# Install only necessary dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
       default-mysql-client \
       libpng-dev \
       libjpeg62-turbo-dev \
       libfreetype6-dev \
       libzip-dev \
       unzip \
       curl \
       libonig-dev \
       libxml2-dev \
       libssl-dev \
       zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
       pdo_mysql \
       mbstring \
       zip \
       exif \
       bcmath \
       gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy PHP configuration file
COPY php/php.ini /usr/local/etc/php/php.ini

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application source code
COPY source/ .  

# Install project dependencies
RUN composer install --no-interaction --optimize-autoloader

# Set entrypoint
ENTRYPOINT ["sh", "/var/www/store/setup.sh"]

# Expose PHP-FPM port
EXPOSE 9000