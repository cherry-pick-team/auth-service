#!/bin/sh
set -e

echo "Migrating..."
php artisan migrate

# Then execute CMD
# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php "$@"
fi

exec "$@"
