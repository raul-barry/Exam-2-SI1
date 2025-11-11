$uri = "http://localhost:8000/api/auth/login"
$body = @{
    login = "admin"
    contrasena = "12345678"
} | ConvertTo-Json

Write-Host "Enviando login..."
Write-Host "Body: $body"

$response = Invoke-WebRequest -Uri $uri -Method Post -Body $body -ContentType "application/json" -UseBasicParsing

Write-Host "Status Code: $($response.StatusCode)"
Write-Host "Content: $($response.Content)"
