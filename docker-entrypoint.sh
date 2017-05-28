#!/bin/bash
set -e

echo "Migrating..."
php artisan migrate

echo "Starting php-fpm..."
php-fpm -e
