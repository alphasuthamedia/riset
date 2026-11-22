FROM node:26.13.1-trixie-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM scratch AS pages
COPY --from=assets /app/public/build /build

FROM dunglas/frankenphp:1.14.0-php8.6.0-alpine AS base
RUN install-php-extensions pcntl pdo_pgsql

FROM base AS vendor
RUN install-php-extensions zip
COPY --from=composer/composer:2.11.2-bin /composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --optimize --no-scripts

FROM base
WORKDIR /app
COPY --from=vendor /app ./
COPY --from=assets /app/public/build/manifest.json ./public/build/manifest.json
CMD php artisan migrate --force && php artisan optimize && exec php artisan octane:frankenphp --host=0.0.0.0 --port=8000 --workers=auto --max-requests=500