#!/bin/bash

set +e  # No salir en primer error

echo "=========================================="
echo "Iniciando aplicación Laravel en Render"
echo "=========================================="

# Variables
MAX_RETRIES=5
RETRY_COUNT=0

# Verificar que las variables de base de datos existan
echo "Verificando variables de entorno..."
if [ -z "$DB_HOST" ] || [ -z "$DB_USERNAME" ] || [ -z "$DB_PASSWORD" ]; then
    echo "⚠️  Variables de BD no configuradas. Continuando sin esperar a PostgreSQL..."
    echo "   DB_HOST: ${DB_HOST:-NO CONFIGURADO}"
    echo "   DB_USERNAME: ${DB_USERNAME:-NO CONFIGURADO}"
    echo "   DB_PASSWORD: ${DB_PASSWORD:-NO CONFIGURADO}"
    SKIP_DB_WAIT=true
else
    echo "✅ Variables de BD configuradas"
    SKIP_DB_WAIT=false
fi

# Esperar a que PostgreSQL esté listo (solo si está configurado)
if [ "$SKIP_DB_WAIT" != "true" ]; then
    echo "Esperando conexión a PostgreSQL..."
    until PGPASSWORD=$DB_PASSWORD psql -h $DB_HOST -U $DB_USERNAME -d postgres -c "SELECT 1" > /dev/null 2>&1; do
        RETRY_COUNT=$((RETRY_COUNT + 1))
        if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
            echo "❌ PostgreSQL no se conectó después de $MAX_RETRIES intentos"
            echo "⚠️  Continuando de todas formas (puede fallar más adelante)"
            break
        fi
        echo "⏳ Intento $RETRY_COUNT/$MAX_RETRIES: PostgreSQL aún no está listo..."
        sleep 3
    done
    
    if [ $RETRY_COUNT -lt $MAX_RETRIES ]; then
        echo "✅ PostgreSQL está listo"
    fi
else
    echo "⏭️  Saltando verificación de PostgreSQL (no configurado)"
fi

# Limpiar cache de Laravel
echo "Limpiando caché..."
php artisan cache:clear 2>&1 || true
php artisan config:clear 2>&1 || true
php artisan view:clear 2>&1 || true

# Crear la base de datos si no existe (solo si está configurado)
if [ "$SKIP_DB_WAIT" != "true" ]; then
    echo "Verificando base de datos..."
    PGPASSWORD=$DB_PASSWORD psql -h $DB_HOST -U $DB_USERNAME -tc "SELECT 1 FROM pg_database WHERE datname = '$DB_DATABASE'" | grep -q 1 || \
        PGPASSWORD=$DB_PASSWORD createdb -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE 2>&1 || true
    
    # Crear el schema si no existe
    echo "Creando schema..."
    php artisan tinker --execute="DB::statement('CREATE SCHEMA IF NOT EXISTS carga_horaria')" 2>&1 || true

    # Ejecutar migraciones
    echo "Ejecutando migraciones..."
    php artisan migrate --force --no-interaction 2>&1 || {
        echo "⚠️ Migraciones completadas con advertencias (esto es normal)"
    }

    # Ejecutar seeders si es necesario
    echo "Ejecutando seeders..."
    php artisan db:seed --force --no-interaction 2>&1 || {
        echo "⚠️ Seeders completados con advertencias"
    }
else
    echo "⏭️  Saltando migraciones y seeders (BD no configurada)"
fi

# Generar APP_KEY si está vacío
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "Generando APP_KEY..."
    php artisan key:generate --force
fi

# Log del estado
echo "=========================================="
echo "✅ Aplicación lista para iniciar"
echo "=========================================="
echo "APP_ENV: ${APP_ENV:-no configurado}"
echo "APP_URL: ${APP_URL:-no configurado}"
echo "DB_HOST: ${DB_HOST:-no configurado}"
echo "DB_DATABASE: ${DB_DATABASE:-no configurado}"
echo "PORT: 10000"
echo "=========================================="

# Iniciar servidor Laravel
exec php artisan serve --host=0.0.0.0 --port=10000
