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

COPY docker-entrypoint.sh /usr/local/bin/like-girl-entrypoint
RUN chmod +x /usr/local/bin/like-girl-entrypoint

ENV LANG="C.UTF-8" \
    TZ="Asia/Shanghai" \
    PUID=0 \
    PGID=0 \
    UMASK=000"

ENV LIKEGIRL_SQLITE_PATH=/var/www/html/data/likegirl.sqlite
ENV LIKEGIRL_SQLITE_SEED=/var/www/html/love_db.sql

RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data

RUN LIKEGIRL_SQLITE_PATH=/tmp/likegirl-test.sqlite php -r 'include "/var/www/html/admin/Config_DB.php"; include "/var/www/html/admin/SqliteCompat.php"; $connect = mysqli_connect("", "", "", ""); if (!$connect) { fwrite(STDERR, "SQLite init failed\n"); exit(1); }' \
    && rm -f /tmp/likegirl-test.sqlite*

VOLUME ["/var/www/html/data"]

ENTRYPOINT ["like-girl-entrypoint"]
CMD ["apache2-foreground"]

EXPOSE 80
