#!/bin/sh
set -e  # Exit script on any error

echo "Starting setup script..."

# Change only the group (keep the user unchanged)
echo "Setting correct group ownership..."
chgrp -R www-data /var/www/store

# Install dependencies if not already installed
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install
else
    echo "Dependencies already installed, skipping..."
fi

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm
