#!/bin/sh

echo "MySQL is up - Running database setup..."

# Run Symfony migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Seed the database (if you have fixtures)
if [ -f bin/console ]; then
    php bin/console doctrine:fixtures:load --no-interaction
fi

echo "Database setup complete!"

# Start PHP-FPM
php-fpm
