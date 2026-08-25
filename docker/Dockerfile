FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libonig-dev \
        libsqlite3-dev \
    && docker-php-ext-install \
        curl \
        mbstring \
        pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . /var/www/html/

# 将 Apache 文档根指向 public/，使 app/、storage/、database/ 等
# 应用代码与数据目录无法被 URL 直接访问。
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

COPY docker/docker-entrypoint.sh /usr/local/bin/like-girl-entrypoint
RUN chmod +x /usr/local/bin/like-girl-entrypoint

ENV LANG="C.UTF-8" \
    TZ="Asia/Shanghai" \
    PUID=0 \
    PGID=0 \
    UMASK=000

ENV LIKEGIRL_SQLITE_PATH=/var/www/html/storage/data/likegirl.sqlite \
    LIKEGIRL_SQLITE_SEED=/var/www/html/database/love_db.sql

RUN mkdir -p /var/www/html/storage/data /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage

# 镜像构建期验证 SQLite 兼容层与数据库播种可正常工作
RUN LIKEGIRL_SQLITE_PATH=/tmp/likegirl-test.sqlite php -r 'include "/var/www/html/app/Core/SqliteCompat.php"; include "/var/www/html/app/config/database.php"; $connect = mysqli_connect("", "", "", ""); if (!$connect) { fwrite(STDERR, "SQLite init failed\n"); exit(1); }' \
    && rm -f /tmp/likegirl-test.sqlite*

VOLUME ["/var/www/html/storage/data"]

ENTRYPOINT ["like-girl-entrypoint"]
CMD ["apache2-foreground"]

EXPOSE 80
