# Test script for new dashboard endpoints
# First, we need to get a valid token

$base_url = "http://localhost:8000"

# Step 1: Get token
Write-Host "=== GETTING TOKEN ===" -ForegroundColor Cyan

$login_response = Invoke-WebRequest -Uri "$base_url/api/login" `
    -Method POST `
    -ContentType "application/json" `
    -Body @{
        email = "admin@example.com"
        password = "123456789"
    } | ConvertFrom-Json

$token = $login_response.token
Write-Host "✓ Token obtained: $($token.Substring(0, 20))..." -ForegroundColor Green

# Create headers with token
$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
    "Content-Type" = "application/json"
}

# Step 2: Test KPIs endpoint
Write-Host "`n=== TESTING /api/dashboard/kpis ===" -ForegroundColor Cyan
try {
    $kpis_response = Invoke-WebRequest -Uri "$base_url/api/dashboard/kpis" `
        -Method GET `
        -Headers $headers | ConvertFrom-Json
    Write-Host "✓ Status: 200 OK" -ForegroundColor Green
    Write-Host "KPIs Data:"
    Write-Host ($kpis_response | ConvertTo-Json -Depth 3)
} catch {
    Write-Host "✗ Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 3: Test Coordinación endpoint
Write-Host "`n=== TESTING /api/dashboard/coordinacion ===" -ForegroundColor Cyan
try {
    $coord_response = Invoke-WebRequest -Uri "$base_url/api/dashboard/coordinacion" `
        -Method GET `
        -Headers $headers | ConvertFrom-Json
    Write-Host "✓ Status: 200 OK" -ForegroundColor Green
    Write-Host "Coordinación Data:"
    Write-Host ($coord_response | ConvertTo-Json -Depth 3)
} catch {
    Write-Host "✗ Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 4: Test Bitácora endpoint
Write-Host "`n=== TESTING /api/dashboard/bitacora ===" -ForegroundColor Cyan
try {
    $bitacora_response = Invoke-WebRequest -Uri "$base_url/api/dashboard/bitacora" `
        -Method GET `
        -Headers $headers | ConvertFrom-Json
    Write-Host "✓ Status: 200 OK" -ForegroundColor Green
    Write-Host "Bitácora Data (first 5 records):"
    Write-Host ($bitacora_response | ConvertTo-Json -Depth 3)
} catch {
    Write-Host "✗ Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n✅ TEST COMPLETED" -ForegroundColor Green
