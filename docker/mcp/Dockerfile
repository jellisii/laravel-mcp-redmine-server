FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    curl \
    libzip-dev \
    sqlite-dev \
    && docker-php-ext-install \
        pdo_sqlite \
        zip

WORKDIR /var/www/mcp-test

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --no-interaction --optimize-autoloader --no-progress

COPY docker/mcp/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
