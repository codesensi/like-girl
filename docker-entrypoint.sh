#!/bin/sh
set -e

mkdir -p /var/www/html/data
if ! chown -R www-data:www-data /var/www/html/data; then
    echo "Warning: unable to chown /var/www/html/data; SQLite may be unable to write there." >&2
fi

exec docker-php-entrypoint "$@"
