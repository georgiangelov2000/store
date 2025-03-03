#!/bin/sh
set -e  # Exit script on any error

echo "Starting setup script..."

if [ ! -d "vendor" ]; then
    composer install
else
    echo "Dependencies already installed, skipping..."
fi

# Start PHP-FPM
exec php-fpm
