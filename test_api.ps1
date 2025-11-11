$uri = "http://localhost:8000/api/auth/login"
$body = @{
    login = "admin"
    contrasena = "12345678"
} | ConvertTo-Json

$response = Invoke-WebRequest -Uri $uri -Method Post -Body $body -ContentType "application/json" -UseBasicParsing

$token = ($response.Content | ConvertFrom-Json).token

Write-Host "Token: $token"
Write-Host ""

# Ahora obtener los roles
$rolesUri = "http://localhost:8000/api/roles"
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

$rolesResponse = Invoke-WebRequest -Uri $rolesUri -Method Get -Headers $headers -UseBasicParsing

Write-Host "Roles Response:"
$rolesResponse.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10
