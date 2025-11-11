@echo off
setlocal enabledelayedexpansion

REM Hacer login
for /f "delims=" %%A in ('powershell -Command "Invoke-WebRequest -Uri 'http://localhost:8000/api/auth/login' -Method Post -Body '{\"login\":\"admin\",\"contrasena\":\"12345678\"}' -ContentType 'application/json' -UseBasicParsing | Select-Object -ExpandProperty Content"') do (
    set "response=%%A"
)

echo Response: !response!

REM Extraer token (es un poco complicado en batch, mejor usa PowerShell)
echo Usa test_login.ps1 en PowerShell
