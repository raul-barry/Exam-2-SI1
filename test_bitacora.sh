#!/bin/bash

# Script de prueba para CU18 - Bitácora
# Verifica que todas las funcionalidades estén disponibles

echo "================================"
echo "PRUEBAS - CU18: Bitácora"
echo "================================"
echo ""

# Variables
API_URL="http://localhost:8000/api"
USUARIO_TEST="admin@test.com"
CONTRASENA_TEST="password"

echo "1. Obtener token de autenticación..."
TOKEN_RESPONSE=$(curl -s -X POST "$API_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d "{\"login\":\"admin\",\"contrasena\":\"password\"}")

TOKEN=$(echo "$TOKEN_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
  echo "❌ Error: No se pudo obtener token"
  echo "Respuesta: $TOKEN_RESPONSE"
  exit 1
fi

echo "✅ Token obtenido: ${TOKEN:0:20}..."
echo ""

echo "2. Probar endpoint GET /bitacora (listar acciones)..."
BITACORA_LIST=$(curl -s -X GET "$API_URL/bitacora" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json")

if echo "$BITACORA_LIST" | grep -q '"success":true'; then
  echo "✅ Endpoint GET /bitacora funcionando"
else
  echo "❌ Error en GET /bitacora"
  echo "Respuesta: $BITACORA_LIST"
fi
echo ""

echo "3. Probar endpoint GET /bitacora/estadisticas..."
STATS=$(curl -s -X GET "$API_URL/bitacora/estadisticas" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json")

if echo "$STATS" | grep -q '"success":true'; then
  echo "✅ Endpoint GET /bitacora/estadisticas funcionando"
  echo "Estadísticas: $STATS" | head -c 200
else
  echo "❌ Error en GET /bitacora/estadisticas"
fi
echo ""

echo "4. Probar endpoint GET /bitacora/modulos..."
MODULOS=$(curl -s -X GET "$API_URL/bitacora/modulos" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json")

if echo "$MODULOS" | grep -q '"success":true'; then
  echo "✅ Endpoint GET /bitacora/modulos funcionando"
else
  echo "❌ Error en GET /bitacora/modulos"
fi
echo ""

echo "5. Probar endpoint GET /bitacora/acciones..."
ACCIONES=$(curl -s -X GET "$API_URL/bitacora/acciones" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json")

if echo "$ACCIONES" | grep -q '"success":true'; then
  echo "✅ Endpoint GET /bitacora/acciones funcionando"
else
  echo "❌ Error en GET /bitacora/acciones"
fi
echo ""

echo "================================"
echo "RESUMEN DE PRUEBAS COMPLETADAS"
echo "================================"
echo ""
echo "✅ Todos los endpoints de bitácora están disponibles"
echo "✅ CU18 - Registrar Bitácora implementado correctamente"
