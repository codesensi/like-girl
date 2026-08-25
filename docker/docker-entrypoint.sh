#!/bin/sh
set -e

mkdir -p /var/www/html/storage/data /var/www/html/storage/logs
if ! chown -R www-data:www-data /var/www/html/storage; then
    echo "Warning: unable to chown /var/www/html/storage; SQLite may be unable to write there." >&2
fi

# 创建 IP 日志文件并赋予写入权限（storage 目录位于文档根 public/ 之外，无法被 URL 下载）
touch /var/www/html/storage/logs/ip.log 2>/dev/null || true
chown www-data:www-data /var/www/html/storage/logs/ip.log 2>/dev/null || true

exec docker-php-entrypoint "$@"
