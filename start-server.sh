#!/bin/bash
set -e

echo "=========================================="
echo "Iniciando aplicación Laravel"
echo "=========================================="

# Esperar más tiempo a que la red y DNS estén listos
echo "Esperando 20 segundos para que DNS y red estén listos..."
sleep 20

# Mostrar variables de entorno (para debugging)
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"
echo "DB_USERNAME: $DB_USERNAME"

# Intentar resolver el host
echo "Resolviendo host $DB_HOST..."
nslookup $DB_HOST || echo "⚠️ nslookup falló, continuando..."

# Intentar conectar a la base de datos con reintentos más agresivos
echo "Intentando conectar a PostgreSQL..."
CONNECTED=false
for i in {1..20}; do
  echo "Intento $i/20..."
  
  if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Conectado a DB';" 2>&1 | grep -q "Conectado a DB"; then
    echo "✓ PostgreSQL está listo"
    CONNECTED=true
    break
  fi
  
  if [ $i -lt 20 ]; then
    echo "  Esperando 3 segundos..."
    sleep 3
  fi
done

if [ "$CONNECTED" = false ]; then
  echo "⚠️ No se pudo conectar a PostgreSQL, continuando de todas formas..."
fi

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force 2>&1 | head -20 || echo "Migraciones completadas o no requeridas"

# Crear el schema
echo "Creando schema..."
php artisan tinker --execute="DB::statement('CREATE SCHEMA IF NOT EXISTS carga_horaria')" 2>&1 || echo "Schema creado o ya existe"

# Iniciar servidor
echo "=========================================="
echo "✓ Iniciando servidor Laravel en puerto 10000"
echo "=========================================="
php artisan serve --host=0.0.0.0 --port=10000
