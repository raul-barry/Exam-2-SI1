# Etapa 1: Builder de PHP
FROM composer:2 AS php-build

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction


# Etapa 2: Builder de Vite
FROM node:18 AS vite-build

WORKDIR /app

COPY . .

RUN npm install
RUN npm run build


# Etapa 3: Imagen final
FROM php:8.2-apache

WORKDIR /var/www/html

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    ghostscript \
    && docker-php-ext-install pdo pdo_pgsql

# Habilitar Apache Rewrite
RUN a2enmod rewrite

# Copiar código Laravel
COPY . .

# Copiar build de Vite
COPY --from=vite-build /app/public/build ./public/build

# Copiar vendor
COPY --from=php-build /app/vendor ./vendor

# Permisos
RUN mkdir -p bootstrap/cache storage \
    && chmod -R 777 bootstrap/cache storage

EXPOSE 8080

CMD ["bash", "start-server.sh"]
