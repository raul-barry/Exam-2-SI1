#!/bin/bash

# Script de verificación pre-deployment para Render
# Ejecutar antes de hacer push: bash verify-render-deployment.sh

set -e

echo "=========================================="
echo "🔍 VERIFICACIÓN PRE-DEPLOYMENT PARA RENDER"
echo "=========================================="

ERRORS=0
WARNINGS=0

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✅${NC} Archivo existe: $1"
        return 0
    else
        echo -e "${RED}❌${NC} Archivo falta: $1"
        ERRORS=$((ERRORS + 1))
        return 1
    fi
}

check_string_in_file() {
    if grep -q "$2" "$1" 2>/dev/null; then
        echo -e "${GREEN}✅${NC} $1 contiene: $2"
        return 0
    else
        echo -e "${YELLOW}⚠️${NC}  $1 NO contiene: $2"
        WARNINGS=$((WARNINGS + 1))
        return 1
    fi
}

echo ""
echo "📋 Verificando archivos críticos..."
echo ""

check_file "composer.json"
check_file "package.json"
check_file "Dockerfile"
check_file "render.yaml"
check_file ".env.example"
check_file ".env.production"
check_file "start-server.sh"
check_file "init-database.sh"

echo ""
echo "🔧 Verificando configuración..."
echo ""

check_string_in_file "Dockerfile" "FROM php:8.2-apache"
check_string_in_file "render.yaml" "APP_DEBUG"
check_string_in_file "render.yaml" "databasePostgres"
check_string_in_file ".env.production" "APP_ENV=production"
check_string_in_file ".env.production" "APP_DEBUG=false"
check_string_in_file ".env.production" "DB_SSLMODE=require"
check_string_in_file "start-server.sh" "\${PORT:-10000}"

echo ""
echo "📦 Verificando dependencias..."
echo ""

if [ -f "composer.lock" ]; then
    echo -e "${GREEN}✅${NC} composer.lock existe"
else
    echo -e "${YELLOW}⚠️${NC}  composer.lock no existe (se generará en build)"
    WARNINGS=$((WARNINGS + 1))
fi

if [ -f "package-lock.json" ] || [ -f "package.json" ]; then
    echo -e "${GREEN}✅${NC} npm dependencies están configuradas"
else
    echo -e "${RED}❌${NC} npm no está configurado"
    ERRORS=$((ERRORS + 1))
fi

echo ""
echo "🗄️  Verificando base de datos..."
echo ""

if [ -d "database/migrations" ]; then
    count=$(find database/migrations -name "*.php" -type f | wc -l)
    echo -e "${GREEN}✅${NC} Se encontraron $count migraciones"
else
    echo -e "${YELLOW}⚠️${NC}  No se encontró carpeta de migraciones"
fi

if [ -d "database/seeders" ]; then
    echo -e "${GREEN}✅${NC} Carpeta de seeders existe"
else
    echo -e "${YELLOW}⚠️${NC}  No se encontró carpeta de seeders"
fi

echo ""
echo "🎨 Verificando frontend..."
echo ""

if [ -f "vite.config.js" ]; then
    echo -e "${GREEN}✅${NC} vite.config.js existe"
else
    echo -e "${YELLOW}⚠️${NC}  vite.config.js no encontrado"
fi

echo ""
echo "🔐 Verificando seguridad..."
echo ""

if grep -q "APP_KEY=base64:" ".env.production"; then
    echo -e "${GREEN}✅${NC} APP_KEY está configurada"
else
    echo -e "${RED}❌${NC} APP_KEY no está configurada"
    ERRORS=$((ERRORS + 1))
fi

if grep -q "APP_DEBUG=false" ".env.production"; then
    echo -e "${GREEN}✅${NC} APP_DEBUG está en false para producción"
else
    echo -e "${RED}❌${NC} APP_DEBUG no está en false"
    ERRORS=$((ERRORS + 1))
fi

echo ""
echo "=========================================="
echo "📊 RESUMEN DE VERIFICACIÓN"
echo "=========================================="
echo -e "Errores encontrados: ${RED}${ERRORS}${NC}"
echo -e "Advertencias: ${YELLOW}${WARNINGS}${NC}"

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✅ El proyecto está listo para desplegarse en Render${NC}"
    exit 0
else
    echo -e "${RED}❌ Hay errores que deben corregirse antes de desplegar${NC}"
    exit 1
fi
