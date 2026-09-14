FROM node:20-alpine AS frontend-builder

WORKDIR /build

COPY package*.json ./
RUN npm ci --legacy-peer-deps

COPY . .
RUN npm run build

# Production PHP image
FROM php:8.2-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
        $PHPIZE_DEPS \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
        mariadb-connector-c-dev \
        sqlite-dev \
        unzip \
        curl \
    && docker-php-ext-install mbstring pdo_mysql pdo_sqlite zip intl \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .

# Copy the built frontend assets that exist in this project.
# Some stacks only generate .next and static public assets, not an out/ directory.
COPY --from=frontend-builder /build/public ./public
COPY --from=frontend-builder /build/.next ./.next

RUN rm -f bootstrap/cache/*.php \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chmod -R ug+rwx storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PORT=8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
  CMD curl -fsS "http://127.0.0.1:${PORT:-8080}/up" || exit 1

EXPOSE 8080

CMD ["sh", "docker/start.sh"]