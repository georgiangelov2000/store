#!/bin/sh
set -e  # Exit on error

echo "Starting setup script..."

# Check for pending migrations
PENDING_MIGRATIONS=$(php bin/console doctrine:migrations:status | grep "New Migrations" | awk '{print $3}')

if [ "$PENDING_MIGRATIONS" -gt 0 ]; then
    echo "Running migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction
    php bin/console doctrine:fixtures:load --no-interaction

    echo "Migrations executed successfully!"
else
    echo "No new migrations to run."
fi

exec php-fpm
