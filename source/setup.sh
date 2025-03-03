#!/bin/sh
set -e  # Exit script on any error

echo "Starting setup script..."

# Ensure the correct user is detected dynamically
CURRENT_UID=$(id -u)
CURRENT_USER=$(getent passwd "$CURRENT_UID" | cut -d: -f1)

echo "Current user: $CURRENT_USER (UID: $CURRENT_UID)"

# Set correct permissions
echo "Setting correct ownership..."
chown -R "$CURRENT_USER":www-data /var/www/store

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
