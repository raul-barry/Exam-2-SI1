FROM php:8.2-apache

WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    postgresql-client \
    npm \
    unzip \
    libapache2-mod-php8.2 \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite \
    && a2enmod headers \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
COPY . .

# Crear directorios necesarios con permisos
RUN mkdir -p storage/logs bootstrap/cache && chmod -R 777 storage bootstrap

# Instalar dependencias PHP (sin ejecutar migraciones)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar y compilar frontend
RUN npm install && npm run build

# Configurar Apache para Laravel
RUN echo '<VirtualHost *:8080>' > /etc/apache2/sites-available/000-default.conf && \
    echo '    DocumentRoot /app/public' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    <Directory /app/public>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</VirtualHost>'

# Hacer ejecutables los scripts
RUN chmod +x start-server.sh init-database.sh

# Exponer puerto 8080 (usado por Render)
EXPOSE 8080

# Comando de inicio
CMD ["bash", "start-server.sh"]
