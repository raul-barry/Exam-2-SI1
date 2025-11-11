# CU14 - Registrar Asistencia mediante QR

## Descripción General

El caso de uso 14 permite registrar la asistencia de docentes a través de códigos QR. Los coordinadores académicos o docentes pueden generar un código QR único para cada sesión de clase, que expira después de un tiempo determinado. Los estudiantes pueden escanear el QR o usar el enlace directo para registrar su asistencia de forma rápida y segura.

## Flujo del Caso de Uso

```
1. Docente/Coordinador genera QR
   ├─ Selecciona asignación
   ├─ Define duración (5 min - 8 horas)
   └─ Genera QR y enlace

2. Sistema crea sesión temporal
   ├─ Genera token único (32 caracteres)
   ├─ Crea registro en BD
   └─ Genera código QR con URL

3. Estudiante escanea QR
   ├─ Abre formulario de registro
   └─ Ve información de clase

4. Estudiante registra asistencia
   ├─ Completa formulario
   ├─ Selecciona estado
   └─ Envía datos

5. Sistema valida y registra
   ├─ Verifica token vigente
   ├─ Crea registro de asistencia
   └─ Cierra sesión
```

## Tecnología Implementada

### Backend
- **Librería QR:** `endroid/qr-code v6.0.9`
- **Controlador:** `RegistroAsistenciaController.php`
- **Modelo:** `SesionAsistencia.php`
- **Base de Datos:** `sesiones_asistencia` table
- **Servicio:** `QRCodeService.php`

### Frontend
- **Componente Generación:** `GenerarQR.jsx`
- **Componente Registro:** `RegistroAsistencia.jsx`
- **Estilos:** CSS con gradientes y animaciones

## Estructura de Archivos

```
app/
├─ Http/Controllers/Asistencia_Docente/
│  └─ RegistroAsistenciaController.php     ← Controlador principal
├─ Models/
│  └─ SesionAsistencia.php                 ← Modelo de sesiones
├─ Services/
│  └─ QRCodeService.php                    ← Servicio de generación QR
└─ database/migrations/
   └─ 2025_11_11_000000_create_sesiones_asistencia_table.php

resources/js/pages/
├─ GenerarQR.jsx                           ← Interfaz para generar QR
├─ GenerarQR.css                           ← Estilos generador
├─ RegistroAsistencia.jsx                  ← Formulario de registro
└─ RegistroAsistencia.css                  ← Estilos registro
```

## Endpoints API

### 1. Generar Sesión de Asistencia (QR)
```
POST /api/asistencia/generar-qr
Autenticación: Requerida (auth:sanctum)

Parámetros:
{
  "id_asignacion": 1,           // ID de la asignación
  "duracion_minutos": 60        // Duración de la sesión (opcional, default: 60)
}

Respuesta Exitosa (201):
{
  "success": true,
  "message": "Sesión de asistencia generada exitosamente",
  "data": {
    "id_sesion": 1,
    "token": "abc123def456...",
    "url_registro": "https://app.com/asistencia/registro/abc123def456...",
    "qr_base64": "data:image/png;base64,...",
    "fecha_expiracion": "2025-11-11T14:30:00",
    "duracion_minutos": 60
  }
}
```

### 2. Mostrar Formulario de Registro
```
GET /api/asistencia/registro/{token}
Autenticación: No requerida (Público)

Respuesta Exitosa (200):
{
  "success": true,
  "data": {
    "sesion": {
      "id_sesion": 1,
      "token": "abc123def456...",
      "fecha_expiracion": "2025-11-11T14:30:00",
      "tiempo_restante": 45
    },
    "asignacion": {
      "id_asignacion": 1,
      "docente": "Dr. Juan Pérez",
      "codigo_docente": "DOC001",
      "materia": "Programación II",
      "grupo": "A",
      "aula": "101",
      "estado": "ACTIVO"
    }
  }
}

Errores:
- 404: Sesión no encontrada
- 410: Sesión expirada o cerrada
```

### 3. Registrar Asistencia
```
POST /api/asistencia/registrar
Autenticación: No requerida (Público)

Parámetros:
{
  "token": "abc123def456...",
  "id_asignacion": 1,
  "estado": "ASISTIO",          // ASISTIO | FALTA | JUSTIFICADA
  "observaciones": "Llegué tarde" // Opcional
}

Respuesta Exitosa (201):
{
  "success": true,
  "message": "Asistencia registrada exitosamente",
  "data": {
    "id_asistencias": 1,
    "fecha": "2025-11-11",
    "hora_de_registro": "14:15:30",
    "tipo_registro": "QR",
    "estado": "ASISTIO"
  }
}
```

### 4. Listar Sesiones Activas
```
GET /api/asistencia/sesiones-activas?id_asignacion=1
Autenticación: Requerida (auth:sanctum)

Respuesta:
{
  "success": true,
  "data": [
    {
      "id_sesion": 1,
      "token": "abc123...",
      "id_asignacion": 1,
      "estado": "ACTIVA",
      "fecha_expiracion": "2025-11-11T14:30:00",
      ...
    }
  ]
}
```

### 5. Cerrar Sesión
```
POST /api/asistencia/cerrar-sesion
Autenticación: Requerida (auth:sanctum)

Parámetros:
{
  "id_sesion": 1
}

Respuesta Exitosa (200):
{
  "success": true,
  "message": "Sesión cerrada exitosamente"
}
```

## Funcionalidades

### Para Docentes/Coordinadores

1. **Generar QR**
   - Seleccionar asignación (materia, grupo, aula)
   - Definir duración de validez
   - Obtener código QR y enlace
   - Descargar o compartir

2. **Gestionar Sesiones**
   - Ver sesiones activas
   - Monitorear tiempo restante
   - Cerrar sesiones manualmente

### Para Estudiantes

1. **Registrar Asistencia**
   - Escanear QR o usar enlace
   - Ver detalles de la clase
   - Seleccionar estado (Asistió, Falta, Justificada)
   - Agregar observaciones (opcional)
   - Confirmar registro

## Seguridad

### Validaciones

1. **Token Temporal**
   - Generado con 32 caracteres aleatorios
   - Único por sesión
   - Expira automáticamente

2. **Duración de Sesión**
   - Mínimo: 5 minutos
   - Máximo: 8 horas
   - Verificación en cada acceso

3. **Integridad de Datos**
   - Validación de asignación
   - Verificación de correspondencia token-asignación
   - Bitácora de auditoría completa

### Bitácora

Se registra en `bitacoras` tabla:
- Generación de sesión
- Acceso al formulario
- Registro de asistencia
- Cierre de sesión

## Modelos de Base de Datos

### Tabla: sesiones_asistencia

```sql
CREATE TABLE sesiones_asistencia (
  id_sesion BIGINT PRIMARY KEY AUTO_INCREMENT,
  token VARCHAR(255) UNIQUE NOT NULL,
  id_asignacion INTEGER NOT NULL,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_expiracion TIMESTAMP NOT NULL,
  estado ENUM('ACTIVA', 'CERRADA', 'EXPIRADA') DEFAULT 'ACTIVA',
  url_registro TEXT,
  qr_data LONGTEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  FOREIGN KEY (id_asignacion) REFERENCES asignacion_horario(id_asignacion) ON DELETE CASCADE,
  INDEX idx_token (token),
  INDEX idx_id_asignacion (id_asignacion),
  INDEX idx_estado (estado),
  INDEX idx_fecha_expiracion (fecha_expiracion)
);
```

## Flujo Técnico Detallado

### Generación de QR

```php
1. Validar asignación existe
2. Generar token única: Str::random(32)
3. Crear URL: route('asistencia.registro.form', ['token' => $token])
4. Generar QR con QRCodeService::generarQR($url)
5. Guardar en SesionAsistencia con fecha_expiracion
6. Retornar QR en base64 + datos
7. Registrar en Bitácora
```

### Registro de Asistencia

```php
1. Obtener sesión por token
2. Validar sesión activa y no expirada
3. Verificar asignación coincide
4. Crear registro Asistencia
5. Cerrar sesión (cambiar estado a CERRADA)
6. Registrar en Bitácora
7. Retornar confirmación
```

## Ejemplos de Uso

### Generación desde Frontend

```javascript
const { data } = await api.post('/asistencia/generar-qr', {
  id_asignacion: 1,
  duracion_minutos: 90
});

// Mostrar QR
<img src={data.data.qr_base64} />

// O descargar
const link = document.createElement('a');
link.href = data.data.qr_base64;
link.download = `qr-${data.data.token}.png`;
link.click();
```

### Registro desde Frontend

```javascript
const { data } = await api.post('/asistencia/registrar', {
  token: urlParams.get('token'),
  id_asignacion: 1,
  estado: 'ASISTIO',
  observaciones: 'Asistencia puntual'
});
```

## Limitaciones Actuales

1. No hay límite de usos por QR (puede ser escaneado múltiples veces)
   - Solución futura: Agregar contador de registros por sesión

2. No hay validación de identidad del estudiante
   - Solución futura: Integrar autenticación/PIN

3. QR se genera en base64 en memoria
   - Solución futura: Almacenar en storage si se necesita persistencia

## Mejoras Futuras

1. **Validación de Identidad**
   - Agregar campo DNI/Email en formulario
   - Verificar contra lista de estudiantes

2. **Duplicados**
   - Prevenir registro múltiple del mismo estudiante
   - Historial de intentos

3. **Notificaciones**
   - Email cuando se cierre sesión
   - SMS opcional

4. **Reportes**
   - Asistencia por clase
   - Estadísticas de puntualidad

5. **Integración**
   - Códigos QR estáticos por clase
   - Integración con calendario académico

## Prueba de Endpoints

Consulta el archivo `test_cu14_asistencia.php` para ejemplos de prueba de todos los endpoints.

## Soporte

Para reportes de errores o sugerencias, contacta al equipo de desarrollo.
