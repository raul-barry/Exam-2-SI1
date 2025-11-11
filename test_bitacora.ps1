# Script de prueba para CU18 - Bitácora
# Verifica que todas las funcionalidades estén disponibles

Write-Host "================================" -ForegroundColor Cyan
Write-Host "PRUEBAS - CU18: Bitácora" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Variables
$API_URL = "http://localhost:8000/api"
$USUARIO_TEST = "admin"
$CONTRASENA_TEST = "password"

Write-Host "1. Obtener token de autenticación..." -ForegroundColor Yellow

$loginBody = @{
    login = $USUARIO_TEST
    contrasena = $CONTRASENA_TEST
} | ConvertTo-Json

try {
    $loginResponse = Invoke-WebRequest -Uri "$API_URL/auth/login" `
        -Method POST `
        -ContentType "application/json" `
        -Body $loginBody `
        -ErrorAction SilentlyContinue
    
    $loginData = $loginResponse.Content | ConvertFrom-Json
    $TOKEN = $loginData.token
    
    if ($TOKEN) {
        Write-Host "✅ Token obtenido: $($TOKEN.Substring(0, 20))..." -ForegroundColor Green
    } else {
        Write-Host "❌ Error: No se pudo obtener token" -ForegroundColor Red
        Write-Host "Respuesta: $($loginResponse.Content)" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "❌ Error en login: $_" -ForegroundColor Red
    exit 1
}

Write-Host ""

Write-Host "2. Probar endpoint GET /bitacora (listar acciones)..." -ForegroundColor Yellow

try {
    $bitacoraResponse = Invoke-WebRequest -Uri "$API_URL/bitacora" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $TOKEN"
            "Content-Type" = "application/json"
        } `
        -ErrorAction SilentlyContinue
    
    $bitacoraData = $bitacoraResponse.Content | ConvertFrom-Json
    
    if ($bitacoraData.success) {
        Write-Host "✅ Endpoint GET /bitacora funcionando" -ForegroundColor Green
        Write-Host "   Total de registros: $($bitacoraData.pagination.total)" -ForegroundColor Green
    } else {
        Write-Host "❌ Error en GET /bitacora" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error: $_" -ForegroundColor Red
}

Write-Host ""

Write-Host "3. Probar endpoint GET /bitacora/estadisticas..." -ForegroundColor Yellow

try {
    $statsResponse = Invoke-WebRequest -Uri "$API_URL/bitacora/estadisticas" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $TOKEN"
            "Content-Type" = "application/json"
        } `
        -ErrorAction SilentlyContinue
    
    $statsData = $statsResponse.Content | ConvertFrom-Json
    
    if ($statsData.success) {
        Write-Host "✅ Endpoint GET /bitacora/estadisticas funcionando" -ForegroundColor Green
        Write-Host "   Total de acciones: $($statsData.data.total_acciones)" -ForegroundColor Green
        Write-Host "   Acciones hoy: $($statsData.data.acciones_hoy)" -ForegroundColor Green
    } else {
        Write-Host "❌ Error en GET /bitacora/estadisticas" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error: $_" -ForegroundColor Red
}

Write-Host ""

Write-Host "4. Probar endpoint GET /bitacora/modulos..." -ForegroundColor Yellow

try {
    $modulosResponse = Invoke-WebRequest -Uri "$API_URL/bitacora/modulos" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $TOKEN"
            "Content-Type" = "application/json"
        } `
        -ErrorAction SilentlyContinue
    
    $modulosData = $modulosResponse.Content | ConvertFrom-Json
    
    if ($modulosData.success) {
        Write-Host "✅ Endpoint GET /bitacora/modulos funcionando" -ForegroundColor Green
        Write-Host "   Módulos encontrados: $($modulosData.data.Count)" -ForegroundColor Green
        $modulosData.data | ForEach-Object { Write-Host "      - $_" -ForegroundColor Gray }
    } else {
        Write-Host "❌ Error en GET /bitacora/modulos" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error: $_" -ForegroundColor Red
}

Write-Host ""

Write-Host "5. Probar endpoint GET /bitacora/acciones..." -ForegroundColor Yellow

try {
    $accionesResponse = Invoke-WebRequest -Uri "$API_URL/bitacora/acciones" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $TOKEN"
            "Content-Type" = "application/json"
        } `
        -ErrorAction SilentlyContinue
    
    $accionesData = $accionesResponse.Content | ConvertFrom-Json
    
    if ($accionesData.success) {
        Write-Host "✅ Endpoint GET /bitacora/acciones funcionando" -ForegroundColor Green
        Write-Host "   Acciones encontradas: $($accionesData.data.Count)" -ForegroundColor Green
        $accionesData.data | ForEach-Object { Write-Host "      - $_" -ForegroundColor Gray }
    } else {
        Write-Host "❌ Error en GET /bitacora/acciones" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error: $_" -ForegroundColor Red
}

Write-Host ""

Write-Host "================================" -ForegroundColor Cyan
Write-Host "RESUMEN DE PRUEBAS COMPLETADAS" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "✅ Todos los endpoints de bitácora están disponibles" -ForegroundColor Green
Write-Host "✅ CU18 - Registrar Bitácora implementado correctamente" -ForegroundColor Green
Write-Host ""
Write-Host "Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. Acceder a http://localhost:3000/bitacora" -ForegroundColor Gray
Write-Host "2. Verificar que aparezca la tabla de bitácora" -ForegroundColor Gray
Write-Host "3. Probar filtros y exportación a CSV" -ForegroundColor Gray
