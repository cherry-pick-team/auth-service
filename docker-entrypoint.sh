#!/bin/bash
set -e

echo "Migrating..."
php artisan migrate

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php "$@"
fi

exec "$@"
