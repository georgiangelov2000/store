#!/bin/sh

echo "⏳ Waiting for MySQL to be ready..."
until mysqladmin ping -h"mysql_db" --silent; do
  sleep 2
done

echo "✅ MySQL is up - Running database setup..."

# Install dependencies
composer install --no-interaction

# Ensure .env is loaded
export $(grep -v '^#' .env | xargs)

# Run Symfony migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Seed the database (if you have fixtures)
if [ -f bin/console ]; then
    php bin/console doctrine:fixtures:load --no-interaction
fi

echo "🎉 Database setup complete!"

# Start PHP-FPM
php-fpm
