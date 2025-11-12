FROM php:8.2-fpm

WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    npm \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
COPY . .

# Crear directorios necesarios ANTES de composer
RUN mkdir -p storage/logs bootstrap/cache && chmod -R 777 storage bootstrap

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar y compilar frontend
RUN npm install && npm run build

# Generar APP_KEY si no existe
RUN php artisan key:generate --force || true

# Exponer puerto
EXPOSE 10000

# Comando de inicio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
