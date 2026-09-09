# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------
# Base — FrankenPHP com as extensões que a aplicação usa
# ---------------------------------------------------------------------
FROM dunglas/frankenphp:php8.3 AS base

RUN install-php-extensions \
        pdo_pgsql pdo_sqlite redis intl opcache pcntl zip bcmath gd

WORKDIR /app

# ---------------------------------------------------------------------
# Build — dependências e autoload otimizado
# ---------------------------------------------------------------------
FROM base AS build

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative \
    && php artisan package:discover --ansi

# ---------------------------------------------------------------------
# Assets — build do frontend
# ---------------------------------------------------------------------
FROM node:22-alpine AS assets

WORKDIR /app
COPY package*.json vite.config.js ./
RUN npm ci
COPY resources ./resources
RUN npm run build

# ---------------------------------------------------------------------
# Produção — modo worker do Octane sobre FrankenPHP
# ---------------------------------------------------------------------
FROM base AS production

ENV APP_ENV=production \
    APP_DEBUG=false \
    SERVER_NAME=:8080 \
    OCTANE_SERVER=frankenphp

COPY --from=build /app /app
COPY --from=assets /app/public/build /app/public/build

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://localhost:8080/up || exit 1

CMD ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8080"]
