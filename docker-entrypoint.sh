#!/bin/sh
set -e

mkdir -p /var/www/html/data
if ! chown -R www-data:www-data /var/www/html/data; then
    echo "Warning: unable to chown /var/www/html/data; SQLite may be unable to write there." >&2
fi

# 创建 IP 日志文件并赋予写入权限
touch /var/www/html/ip.txt 2>/dev/null || true
chown www-data:www-data /var/www/html/ip.txt 2>/dev/null || true

exec docker-php-entrypoint "$@"
