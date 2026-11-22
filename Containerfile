FROM node:26.13.0-trixie-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM scratch AS pages
COPY --from=assets /app/public/build /build

FROM composer:2.10.3 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-scripts

FROM dunglas/frankenphp:1.14.0-php8.5
RUN install-php-extensions pcntl
WORKDIR /app
COPY --from=vendor /app ./
COPY --from=assets /app/public/build/manifest.json ./public/build/manifest.json
RUN chmod +x docker/entrypoint.sh
ENTRYPOINT ["docker/entrypoint.sh"]