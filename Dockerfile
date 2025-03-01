# Use official PHP-FPM 8.2 image
FROM php:8.2-fpm

# Define a variable for system dependencies
ENV SYSTEM_DEPENDENCIES="redis-tools default-mysql-client build-essential iputils-ping wget nano \
                          procps net-tools telnet dnsutils lsof htop ffmpeg libpng-dev \
                          libjpeg62-turbo-dev libfreetype6-dev libzip-dev unzip curl \
                          libonig-dev libxml2-dev libssl-dev zlib1g-dev libcurl4-openssl-dev \
                          libreadline-dev libbz2-dev libxslt-dev libmcrypt-dev texlive-extra-utils"

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends $SYSTEM_DEPENDENCIES \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
       pdo_mysql \
       mbstring \
       zip \
       exif \
       pcntl \
       bcmath \
       gd \
       xsl \
       bz2 \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js (specific version setup script)
RUN curl -fsSL https://deb.nodesource.com/setup_23.x -o nodesource_setup.sh \
    && bash nodesource_setup.sh \
    && apt-get install -y nodejs \
    && rm nodesource_setup.sh

# Copy custom PHP configuration
COPY php/php.ini /usr/local/etc/php/php.ini
# Install Composer globally from an official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/store
# Copy the rest of the application files
COPY src /var/www/store

# Expose PHP-FPM port
EXPOSE 9000

# Default to running the PHP-FPM service
CMD ["php-fpm"]
