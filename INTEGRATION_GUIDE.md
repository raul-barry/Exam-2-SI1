# 📖 GUÍA DE INTEGRACIÓN: Cómo Mostrar las Funcionalidades Ocultas en la UI

## Introducción

Las funcionalidades de KPIs, Coordinación de Horario y Bitácora están completamente implementadas en el backend y frontend, pero actualmente están **ocultas de la interfaz de usuario** según tu especificación. Esta guía explica cómo integrarlas en la UI cuando sea necesario.

---

## 1. MOSTRAR KPIS EN EL DASHBOARD

### Paso 1: Agregar Estado para KPIs en Dashboard.jsx

```javascript
// En Dashboard.jsx, dentro del componente principal

const [kpis, setKpis] = useState({
  carga_asignada: {},
  tasa_asistencia: {},
  resolucion_conflictos: {}
});

const [cargandoKpis, setCargandoKpis] = useState(false);
```

### Paso 2: Llamar a obtenerKPIs() en useEffect

```javascript
useEffect(() => {
  const cargarDatos = async () => {
    setCargandoKpis(true);
    try {
      const data = await obtenerKPIs();
      setKpis(data.kpis);
    } catch (error) {
      console.error('Error al cargar KPIs:', error);
    } finally {
      setCargandoKpis(false);
    }
  };
  
  cargarDatos();
}, []);
```

### Paso 3: Agregar Componente Visual en el JSX

```jsx
{/* Sección de KPIs */}
<div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
  {/* KPI 1: Carga Asignada */}
  <div className="bg-white rounded-lg shadow p-6">
    <h3 className="text-lg font-semibold text-gray-700 mb-2">
      Carga Asignada
    </h3>
    <div className="flex items-center justify-between">
      <div>
        <p className="text-3xl font-bold text-blue-600">
          {kpis.carga_asignada?.porcentaje}%
        </p>
        <p className="text-sm text-gray-500">
          {kpis.carga_asignada?.activa} de {kpis.carga_asignada?.total}
        </p>
      </div>
      <div className="text-4xl">📊</div>
    </div>
  </div>

  {/* KPI 2: Tasa de Asistencia */}
  <div className="bg-white rounded-lg shadow p-6">
    <h3 className="text-lg font-semibold text-gray-700 mb-2">
      Tasa de Asistencia
    </h3>
    <div className="flex items-center justify-between">
      <div>
        <p className="text-3xl font-bold text-green-600">
          {kpis.tasa_asistencia?.porcentaje}%
        </p>
        <p className="text-sm text-gray-500">
          {kpis.tasa_asistencia?.confirmadas} confirmadas
        </p>
      </div>
      <div className="text-4xl">✅</div>
    </div>
  </div>

  {/* KPI 3: Resolución de Conflictos */}
  <div className="bg-white rounded-lg shadow p-6">
    <h3 className="text-lg font-semibold text-gray-700 mb-2">
      Resolución de Conflictos
    </h3>
    <div className="flex items-center justify-between">
      <div>
        <p className="text-3xl font-bold text-orange-600">
          {kpis.resolucion_conflictos?.porcentaje}%
        </p>
        <p className="text-sm text-gray-500">
          {kpis.resolucion_conflictos?.resueltos} resueltos
        </p>
      </div>
      <div className="text-4xl">🔧</div>
    </div>
  </div>
</div>
```

---

## 2. MOSTRAR COORDINACIÓN DE HORARIO

### Paso 1: Agregar Estado para Coordinación

```javascript
const [coordinacion, setCoordinacion] = useState({
  docentes_coordinados: 0,
  grupos_coordinados: 0,
  aulas_utilizadas: 0,
  por_periodo: {}
});
```

### Paso 2: Llamar a obtenerCoordinacionHorario()

```javascript
useEffect(() => {
  const cargarCoordinacion = async () => {
    try {
      const data = await obtenerCoordinacionHorario();
      setCoordinacion(data.coordinacion);
    } catch (error) {
      console.error('Error al cargar coordinación:', error);
    }
  };
  
  cargarCoordinacion();
}, []);
```

### Paso 3: Agregar Tabla de Coordinación

```jsx
{/* Tabla de Coordinación de Horario */}
<div className="bg-white rounded-lg shadow p-6 mb-8">
  <h2 className="text-2xl font-bold text-gray-800 mb-6">
    📅 Coordinación de Horario
  </h2>
  
  <div className="grid grid-cols-3 gap-4 mb-6">
    <div className="text-center p-4 bg-blue-50 rounded-lg">
      <p className="text-sm text-gray-600">Docentes Coordinados</p>
      <p className="text-3xl font-bold text-blue-600">
        {coordinacion.docentes_coordinados}
      </p>
    </div>
    <div className="text-center p-4 bg-green-50 rounded-lg">
      <p className="text-sm text-gray-600">Grupos Coordinados</p>
      <p className="text-3xl font-bold text-green-600">
        {coordinacion.grupos_coordinados}
      </p>
    </div>
    <div className="text-center p-4 bg-orange-50 rounded-lg">
      <p className="text-sm text-gray-600">Aulas Utilizadas</p>
      <p className="text-3xl font-bold text-orange-600">
        {coordinacion.aulas_utilizadas}
      </p>
    </div>
  </div>

  {/* Tabla por Período */}
  <table className="w-full text-left text-sm">
    <thead className="bg-gray-100">
      <tr>
        <th className="px-4 py-2">Período</th>
        <th className="px-4 py-2">Docentes</th>
        <th className="px-4 py-2">Grupos</th>
        <th className="px-4 py-2">Aulas</th>
        <th className="px-4 py-2">Asignaciones</th>
      </tr>
    </thead>
    <tbody>
      {Object.entries(coordinacion.por_periodo || {}).map(([periodo, data]) => (
        <tr key={periodo} className="border-b hover:bg-gray-50">
          <td className="px-4 py-2 font-semibold">{periodo}</td>
          <td className="px-4 py-2">{data.docentes}</td>
          <td className="px-4 py-2">{data.grupos}</td>
          <td className="px-4 py-2">{data.aulas}</td>
          <td className="px-4 py-2">{data.asignaciones}</td>
        </tr>
      ))}
    </tbody>
  </table>
</div>
```

---

## 3. MOSTRAR BITÁCORA (Solo para Administradores)

### Paso 1: Verificar Rol del Usuario

```javascript
const { user } = useContext(AuthContext);
const esAdmin = user?.rol?.nombre === 'Administrador';
```

### Paso 2: Agregar Estado para Bitácora

```javascript
const [bitacora, setBitacora] = useState([]);
const [cargandoBitacora, setCargandoBitacora] = useState(false);
const [filtroModulo, setFiltroModulo] = useState(null);
```

### Paso 3: Llamar a obtenerBitacora()

```javascript
useEffect(() => {
  if (!esAdmin) return;
  
  const cargarBitacora = async () => {
    setCargandoBitacora(true);
    try {
      const data = await obtenerBitacora(50, filtroModulo);
      setBitacora(data.bitacora);
    } catch (error) {
      console.error('Error al cargar bitácora:', error);
    } finally {
      setCargandoBitacora(false);
    }
  };
  
  cargarBitacora();
}, [filtroModulo]);
```

### Paso 4: Agregar Componente de Bitácora

```jsx
{/* Sección de Bitácora (Solo para Administradores) */}
{esAdmin && (
  <div className="bg-white rounded-lg shadow p-6">
    <h2 className="text-2xl font-bold text-gray-800 mb-6">
      🔐 Bitácora de Auditoría
    </h2>
    
    {/* Filtro por Módulo */}
    <div className="mb-6">
      <label className="block text-sm font-semibold text-gray-700 mb-2">
        Filtrar por Módulo:
      </label>
      <select
        value={filtroModulo || ''}
        onChange={(e) => setFiltroModulo(e.target.value || null)}
        className="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg"
      >
        <option value="">Todos los módulos</option>
        <option value="Autenticación">Autenticación</option>
        <option value="Planificación Académica">Planificación Académica</option>
        <option value="Gestión de Grupos">Gestión de Grupos</option>
        <option value="Gestión de Aulas">Gestión de Aulas</option>
        <option value="Gestión de Infraestructura">Infraestructura</option>
        <option value="Malla Horaria">Malla Horaria</option>
      </select>
    </div>

    {/* Tabla de Registros */}
    <div className="overflow-x-auto">
      <table className="w-full text-left text-sm">
        <thead className="bg-gray-100">
          <tr>
            <th className="px-4 py-2">Módulo</th>
            <th className="px-4 py-2">Acción</th>
            <th className="px-4 py-2">Usuario</th>
            <th className="px-4 py-2">Fecha</th>
            <th className="px-4 py-2">Registros</th>
          </tr>
        </thead>
        <tbody>
          {bitacora.map((group) => (
            <tr key={group.modulo} className="border-b hover:bg-gray-50">
              <td className="px-4 py-2 font-semibold">{group.modulo}</td>
              <td className="px-4 py-2 max-w-md truncate">
                {group.registros && group.registros[0]?.accion}
              </td>
              <td className="px-4 py-2">
                {group.registros && group.registros[0]?.usuario}
              </td>
              <td className="px-4 py-2 text-xs">
                {group.registros && new Date(group.registros[0]?.fecha).toLocaleDateString()}
              </td>
              <td className="px-4 py-2 bg-blue-100 text-blue-700 rounded px-2 py-1 font-semibold">
                {group.cantidad}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
    
    {bitacora.length === 0 && !cargandoBitacora && (
      <p className="text-gray-500 text-center py-8">
        No hay registros disponibles
      </p>
    )}
  </div>
)}
```

---

## 4. CREAR UN NUEVO COMPONENTE SEPARADO (Alternativa)

Si prefieres crear componentes separados para cada funcionalidad:

### KpisCard.jsx
```javascript
import { useState, useEffect } from 'react';

export default function KpisCard() {
  const [kpis, setKpis] = useState(null);
  
  useEffect(() => {
    cargarKpis();
  }, []);
  
  const cargarKpis = async () => {
    try {
      const response = await fetch('/api/dashboard/kpis', {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      const data = await response.json();
      setKpis(data.kpis);
    } catch (error) {
      console.error('Error:', error);
    }
  };
  
  if (!kpis) return <div>Cargando...</div>;
  
  return (
    <div className="grid grid-cols-3 gap-4">
      {/* Renderizar KPIs */}
    </div>
  );
}
```

### CoordinacionCard.jsx
```javascript
import { useState, useEffect } from 'react';

export default function CoordinacionCard() {
  const [coordinacion, setCoordinacion] = useState(null);
  
  useEffect(() => {
    cargarCoordinacion();
  }, []);
  
  const cargarCoordinacion = async () => {
    try {
      const response = await fetch('/api/dashboard/coordinacion', {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      const data = await response.json();
      setCoordinacion(data.coordinacion);
    } catch (error) {
      console.error('Error:', error);
    }
  };
  
  if (!coordinacion) return <div>Cargando...</div>;
  
  return (
    <div className="bg-white rounded-lg shadow p-6">
      {/* Renderizar coordinación */}
    </div>
  );
}
```

### BitacoraAdmin.jsx
```javascript
import { useState, useEffect, useContext } from 'react';
import { AuthContext } from '../context/AuthContext';

export default function BitacoraAdmin() {
  const { user } = useContext(AuthContext);
  const [bitacora, setBitacora] = useState([]);
  const [filtro, setFiltro] = useState(null);
  
  useEffect(() => {
    if (user?.rol?.nombre !== 'Administrador') return;
    cargarBitacora();
  }, [filtro]);
  
  const cargarBitacora = async () => {
    try {
      const url = new URL('/api/dashboard/bitacora', window.location.origin);
      if (filtro) url.searchParams.append('modulo', filtro);
      
      const response = await fetch(url, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      const data = await response.json();
      setBitacora(data.bitacora);
    } catch (error) {
      console.error('Error:', error);
    }
  };
  
  if (user?.rol?.nombre !== 'Administrador') {
    return <div>No tienes acceso a esta sección</div>;
  }
  
  return (
    <div className="bg-white rounded-lg shadow p-6">
      {/* Renderizar bitácora */}
    </div>
  );
}
```

---

## 5. INTEGRACIÓN EN MONITOREO.jsx

Para mostrar todo en el menú "Monitoreo y Reportes":

```javascript
// En Monitoreo.jsx, actualizar el array items

const [menuItems] = useState([
  {
    id: 'cu16',
    label: 'Dashboard',
    icon: '📊',
    content: 'dashboard'
  },
  {
    id: 'kpis',
    label: 'KPIs',
    icon: '📈',
    content: 'kpis'
  },
  {
    id: 'coordinacion',
    label: 'Coordinación de Horario',
    icon: '📅',
    content: 'coordinacion'
  },
  {
    id: 'bitacora',
    label: 'Bitácora de Auditoría',
    icon: '🔐',
    content: 'bitacora',
    requiresAdmin: true
  }
]);

// En el renderizado condicional:
{selectedContent === 'kpis' && <KpisCard />}
{selectedContent === 'coordinacion' && <CoordinacionCard />}
{selectedContent === 'bitacora' && user?.rol?.nombre === 'Administrador' && <BitacoraAdmin />}
```

---

## 6. VARIABLES DE ENTORNO (Opcional)

Si quieres condicionar visibilidad en desarrollo:

```javascript
// En Dashboard.jsx o componente principal

const MOSTRAR_KPIS = import.meta.env.VITE_MOSTRAR_KPIS === 'true';
const MOSTRAR_COORDINACION = import.meta.env.VITE_MOSTRAR_COORDINACION === 'true';
const MOSTRAR_BITACORA = import.meta.env.VITE_MOSTRAR_BITACORA === 'true';

// En .env:
VITE_MOSTRAR_KPIS=false
VITE_MOSTRAR_COORDINACION=false
VITE_MOSTRAR_BITACORA=false
```

---

## 7. NOTAS IMPORTANTES

✅ **Todos los endpoints están disponibles ahora**
✅ **Frontend ya tiene los métodos implementados**
✅ **Solo necesitas descomultar o agregar el JSX**
✅ **El control de acceso está implementado en el backend**
✅ **Puedes testar directamente desde la consola del navegador**

---

## 8. TESTING EN CONSOLA DEL NAVEGADOR

Una vez que agregues los métodos, puedes verificar en la consola:

```javascript
// Obtener KPIs
const kpis = await fetch('/api/dashboard/kpis', {
  headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
}).then(r => r.json());
console.log(kpis);

// Obtener Coordinación
const coord = await fetch('/api/dashboard/coordinacion', {
  headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
}).then(r => r.json());
console.log(coord);

// Obtener Bitácora (solo admin)
const bitacora = await fetch('/api/dashboard/bitacora?limite=10', {
  headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
}).then(r => r.json());
console.log(bitacora);
```

---

*Última actualización: 2025-01-15*
*Estado: Listo para integración en UI*
