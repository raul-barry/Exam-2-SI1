#!/bin/bash

echo "=========================================="
echo "Iniciando aplicación Laravel"
echo "=========================================="

# Esperar a que PostgreSQL esté listo
sleep 15

# Ejecutar migraciones sin intentar verificar conexión
echo "Ejecutando migraciones..."
php artisan migrate --force 2>&1 || true

# Crear el schema
echo "Creando schema..."
php artisan tinker --execute="DB::statement('CREATE SCHEMA IF NOT EXISTS carga_horaria')" 2>&1 || true

# Iniciar servidor
echo "=========================================="
echo "✓ Iniciando servidor Laravel en puerto 10000"
echo "=========================================="
exec php artisan serve --host=0.0.0.0 --port=10000
