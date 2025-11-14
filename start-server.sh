#!/bin/bash

echo "=========================================="
echo "Iniciando Laravel en Render"
echo "=========================================="

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Migraciones (solo cuando Render inicia el contenedor)
php artisan migrate --force || true

echo "Iniciando servidor Laravel en puerto $PORT..."
php artisan serve --host 0.0.0.0 --port $PORT
