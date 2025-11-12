# Despliegue en Render

## 📋 Configuración Requerida

### Variables de Entorno en Render Dashboard

Agrega estas variables en tu Web Service de Render:

```
APP_NAME=AppWebCargaHoraria
APP_ENV=production
APP_KEY=base64:VPuXqWlyLax+DN2E/gda6wTVtlES3EkJJquGkv3HE1U=
APP_DEBUG=false
APP_URL=https://tu-app.onrender.com
FRONTEND_URL=https://tu-app.onrender.com

DB_CONNECTION=pgsql
DB_HOST=tu-postgresql.render.com
DB_PORT=5432
DB_DATABASE=appwebcargahoraria
DB_USERNAME=postgres
DB_PASSWORD=tu-password
DB_SCHEMA=carga_horaria

VITE_API_URL=https://tu-app.onrender.com/api
```

### Configuración del Servidor

- **Build Command:** (vacío - usa Docker)
- **Start Command:** (vacío - usa Docker)
- **Runtime:** Docker
- **Region:** Oregon (US West)

### Base de Datos PostgreSQL

1. Crea una instancia PostgreSQL en Render
2. Copia el Host de conexión
3. Reemplaza `DB_HOST` con el valor de Render
4. Reemplaza `DB_PASSWORD` con la contraseña generada

## 🚀 Pasos de Despliegue

1. Ve a https://render.com
2. Crea un nuevo **Web Service**
3. Conecta el repositorio GitHub
4. Render detectará automáticamente el Dockerfile
5. Agrega todas las variables de entorno
6. Haz clic en "Create Web Service"
7. Espera 10-15 minutos

## 📊 Características

- ✅ Laravel 11 con PHP 8.2
- ✅ React 18 compilado
- ✅ PostgreSQL con soporte de esquemas
- ✅ Bitácora de acciones automática
- ✅ QR generation para asistencia
- ✅ Health checks configurados
- ✅ Almacenamiento simbólico del storage

## 🔗 URLs Importantes

- **Aplicación:** https://tu-app.onrender.com
- **API:** https://tu-app.onrender.com/api
- **Bitácora:** https://tu-app.onrender.com/bitacora
- **Asistencias:** https://tu-app.onrender.com/asistencias
