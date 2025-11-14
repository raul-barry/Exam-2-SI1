#!/bin/bash

# Script de deployment automático a Render
# Uso: bash deploy-to-render.sh

set -e

echo "=========================================="
echo "🚀 DEPLOYMENT A RENDER"
echo "=========================================="

# Verificar que estamos en el directorio correcto
if [ ! -f "render.yaml" ]; then
    echo "❌ Error: render.yaml no encontrado. Ejecuta este script desde la raíz del proyecto"
    exit 1
fi

echo ""
echo "1️⃣  Verificando configuración..."
bash verify-render-deployment.sh

if [ $? -ne 0 ]; then
    echo "⚠️  Hay problemas de configuración. Continuar de todas formas? (s/n)"
    read -r response
    if [ "$response" != "s" ]; then
        exit 1
    fi
fi

echo ""
echo "2️⃣  Verificando Git..."

if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo "❌ Error: No estamos en un repositorio Git"
    exit 1
fi

BRANCH=$(git rev-parse --abbrev-ref HEAD)
echo "📍 Rama actual: $BRANCH"

if [ "$BRANCH" != "master" ] && [ "$BRANCH" != "main" ]; then
    echo "⚠️  No estás en la rama master/main. ¿Continuar? (s/n)"
    read -r response
    if [ "$response" != "s" ]; then
        exit 1
    fi
fi

echo ""
echo "3️⃣  Verificando cambios..."

if [ -z "$(git status --porcelain)" ]; then
    echo "✅ Sin cambios sin guardar"
else
    echo "📝 Cambios sin guardar:"
    git status --short
    echo ""
    echo "⚠️  Guarda los cambios antes de desplegar"
    exit 1
fi

echo ""
echo "4️⃣  Construyendo Docker localmente (opcional)..."
echo "¿Deseas probar el build de Docker? (s/n)"
read -r response

if [ "$response" = "s" ]; then
    if command -v docker &> /dev/null; then
        echo "🐳 Construyendo imagen Docker..."
        docker build -t exam-2-si1:test .
        echo "✅ Build exitoso"
    else
        echo "⚠️  Docker no está instalado, saltando test de build"
    fi
fi

echo ""
echo "5️⃣  Últimas verificaciones..."

# Verificar que composer.json está completo
if ! grep -q "laravel/framework" composer.json; then
    echo "❌ Error: Laravel framework no está en composer.json"
    exit 1
fi

# Verificar que package.json está completo
if ! grep -q "react" package.json; then
    echo "❌ Error: React no está en package.json"
    exit 1
fi

echo "✅ Todas las verificaciones completadas"

echo ""
echo "=========================================="
echo "✨ PRÓXIMOS PASOS:"
echo "=========================================="
echo ""
echo "1. Ve a https://render.com"
echo "2. Crea un nuevo 'Web Service'"
echo "3. Conecta tu repositorio de GitHub"
echo "4. Selecciona esta rama ($BRANCH)"
echo "5. En 'Build Command': docker build -t Exam-2-SI1 ."
echo "6. En 'Start Command': bash start-server.sh"
echo "7. Agrega la base de datos PostgreSQL desde el panel de Render"
echo "8. Configura las variables de entorno desde render.yaml"
echo "9. Haz deploy!"
echo ""
echo "📚 Variables de entorno requeridas:"
echo "   - Consulta render.yaml para la lista completa"
echo ""
echo "=========================================="
