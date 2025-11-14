# 📘 GUÍA DE DEPLOYMENT A RENDER

## 🔍 Verificación Previa

Antes de desplegar, ejecuta:

```bash
bash verify-render-deployment.sh
```

Este script verifica:
- ✅ Archivos críticos existentes
- ✅ Configuración de Dockerfile (Apache + PHP-FPM)
- ✅ Configuración de render.yaml
- ✅ Variables de entorno correctas
- ✅ Dependencias de PHP y Node.js
- ✅ Migraciones de base de datos
- ✅ Configuración de seguridad

## 🚀 Pasos de Deployment

### 1. Preparar el repositorio
```bash
git add .
git commit -m "Preparación para deployment en Render"
git push origin master
```

### 2. Ir a Render.com
- Ve a https://render.com
- Inicia sesión o crea una cuenta
- Ve a "Dashboard"

### 3. Crear un nuevo Web Service
- Click en "New +" → "Web Service"
- Conecta tu repositorio GitHub (grupo-23-si1-SA)
- Selecciona la rama "master"

### 4. Configurar el Web Service

**General:**
- Name: `Exam-2-SI1` (o como prefieras)
- Runtime: Docker
- Region: Oregon (Oregon es más rápido)
- Plan: Starter (o superior si quieres mejor rendimiento)

**Build & Deploy:**
- Build Command: `docker build -t Exam-2-SI1 .`
- Start Command: `bash start-server.sh`

### 5. Agregar Base de Datos PostgreSQL

En el mismo panel:
- Click en "PostgreSQL"
- Name: `appwebcargahoraria-db`
- Plan: Starter
- Click "Create Database"

### 6. Configurar Variables de Entorno

Render te proporcionará automáticamente:
- `DATABASE_URL` (desde PostgreSQL)

Agrega estas manualmente en el panel:

```
APP_NAME=AppWebCargaHoraria
APP_ENV=production
APP_KEY=base64:VPuXqWlyLax+DN2E/gda6wTVtlES3EkJJquGkv3HE1U=
APP_DEBUG=false
APP_URL=https://Exam-2-SI1.onrender.com
FRONTEND_URL=https://Exam-2-SI1.onrender.com
LOG_LEVEL=error
VITE_API_URL=https://Exam-2-SI1.onrender.com/api
DB_CONNECTION=pgsql
DB_SCHEMA=carga_horaria
DB_SSLMODE=require
```

Nota: El `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD` y `DB_DATABASE` se obtienen de la variable `DATABASE_URL` que genera Render automáticamente.

### 7. Deploy
- Click en "Deploy"
- Espera a que termine la construcción
- Ve a los logs para verificar que todo está correcto

## ✅ Verificación Post-Deployment

Una vez desplegado, verifica:

1. **Health Check**
   ```bash
   curl https://Exam-2-SI1.onrender.com/
   ```
   Debe responder con la página principal

2. **API Login**
   ```bash
   curl -X POST https://Exam-2-SI1.onrender.com/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@example.com","password":"password"}'
   ```

3. **Dashboard**
   - Abre https://Exam-2-SI1.onrender.com/dashboard

4. **Logs en Render**
   - Ve a "Logs" en el panel de Render
   - Verifica que no haya errores

## 🔧 Solución de Problemas

### Error: Database connection failed
**Problema:** No puede conectarse a la base de datos
**Solución:** 
- Verifica que la base de datos PostgreSQL está creada en Render
- Comprueba que `DB_SSLMODE=require` está configurado
- Verifica los logs para más detalles

### Error: 500 Internal Server Error
**Problema:** Error interno del servidor
**Solución:**
- Revisa los logs en Render Dashboard
- Verifica que las migraciones se ejecutaron: `php artisan migrate --force`
- Comprueba que `APP_DEBUG=false` pero con `LOG_LEVEL=error`

### Error: Assets not found
**Problema:** Los estilos CSS no se cargan
**Solución:**
- Verifica que `npm run build` se ejecutó correctamente
- Comprueba que los archivos están en `public/build/`
- Verifica los logs del build

### Servicio se cae después de iniciar
**Problema:** El servicio inicia pero se detiene inmediatamente
**Solución:**
- Revisa los logs: `bash start-server.sh` tiene fallos
- Verifica que Apache está escuchando en puerto 8080
- Comprueba que las migraciones están completas

## 📊 Monitoreo

Render proporciona:
- **Logs**: Ve a "Logs" → Ver logs en tiempo real
- **Metrics**: CPU, memoria, requests
- **Health**: Estado del servicio
- **Events**: Historial de deployments

## 🔄 Redeploy

Para redeploy después de cambios:

```bash
git add .
git commit -m "Cambios para redeploy"
git push origin master
```

Render detectará automáticamente los cambios en el repositorio y redeployará.

O manualmente:
- Ve a Render Dashboard
- Click en el servicio "Exam-2-SI1"
- Click en "Deployments"
- Click en "Deploy" (usa el commit más reciente)

## 🛑 Escalar o Cambiar Plan

Si necesitas más recursos:

1. Ve a Settings del servicio
2. Plan: Cambia a "Standard" o superior
3. Render escalará automáticamente

## 📝 Variables de Entorno Importantes

| Variable | Valor | Notas |
|----------|-------|-------|
| APP_ENV | production | Debe ser production |
| APP_DEBUG | false | Nunca true en producción |
| LOG_LEVEL | error | Para reducir logs |
| DB_SSLMODE | require | Requerido por Render |
| VITE_API_URL | https://tudominio.com/api | Debe ser HTTPS |

## 🆘 Contacto y Soporte

- Documentación de Render: https://render.com/docs
- Laravel Deployment: https://laravel.com/docs/deployment
- React + Vite: https://vitejs.dev/guide/
