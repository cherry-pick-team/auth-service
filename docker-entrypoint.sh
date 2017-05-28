#!/bin/bash
set -e

php artisan migrate

php-fpm -e
