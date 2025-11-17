import '../css/app.css';
import React, { lazy, Suspense } from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import Layout from './components/Layout';
import HomeRedirect from './components/HomeRedirect';
import Login from './pages/Login';

// Lazy loading de componentes pesados para mejorar el rendimiento inicial
const Dashboard = lazy(() => import('./pages/Dashboard'));
const Usuarios = lazy(() => import('./pages/Usuarios'));
const Roles = lazy(() => import('./pages/Roles'));
const Docentes = lazy(() => import('./pages/Docentes'));
const Materias = lazy(() => import('./pages/Materias'));
const Grupos = lazy(() => import('./pages/Grupos'));
const Aulas = lazy(() => import('./pages/Aulas'));
const Infraestructura = lazy(() => import('./pages/Infraestructura'));
const CargaHoraria = lazy(() => import('./pages/CargaHoraria'));
const ConfiguracionMalla = lazy(() => import('./pages/ConfiguracionMalla'));
const Asistencias = lazy(() => import('./pages/Asistencias'));
const GestionInasistencias = lazy(() => import('./pages/GestionInasistencias'));
const RegistroAsistencia = lazy(() => import('./pages/RegistroAsistencia'));
const ConflictosHorario = lazy(() => import('./pages/ConflictosHorario'));
const DisponibilidadAulas = lazy(() => import('./pages/planificacion/DisponibilidadAulas'));
const Monitoreo = lazy(() => import('./pages/monitoreo/Monitoreo'));
const Bitacora = lazy(() => import('./pages/Bitacora'));

// Componente de loading mientras cargan los componentes lazy
const LoadingFallback = () => (
  <div style={{ 
    display: 'flex', 
    justifyContent: 'center', 
    alignItems: 'center', 
    height: '100vh',
    fontSize: '18px',
    color: '#667eea'
  }}>
    Cargando...
  </div>
);

function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Suspense fallback={<LoadingFallback />}>
          <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/" element={<Layout />}>
              <Route index element={<HomeRedirect />} />
              <Route path="/dashboard" element={<Dashboard />} />
              {/* P1: Autenticación y Control de Acceso */}
              <Route path="/usuarios" element={<Usuarios />} />
              <Route path="/roles" element={<Roles />} />
              {/* P2: Gestión de Catálogos Académicos */}
              <Route path="/docentes" element={<Docentes />} />
              <Route path="/materias" element={<Materias />} />
              <Route path="/grupos" element={<Grupos />} />
              <Route path="/aulas" element={<Aulas />} />
              <Route path="/infraestructura" element={<Infraestructura />} />
              {/* P3: Planificación Académica */}
              <Route path="/carga-horaria" element={<CargaHoraria />} />
              <Route path="/configuracion-malla" element={<ConfiguracionMalla />} />
              {/* P4: Asistencia Docente */}
              <Route path="/asistencias" element={<Asistencias />} />
              <Route path="/gestionar-inasistencias" element={<GestionInasistencias />} />
              <Route path="/asistencia/registro/:token" element={<RegistroAsistencia />} />
              {/* CU12: Gestionar Conflictos de Horario */}
              <Route path="/conflictos-horarios" element={<ConflictosHorario />} />
              {/* CU13: Consultar disponibilidad de aulas */}
              <Route path="/disponibilidad-aulas" element={<DisponibilidadAulas />} />
              {/* P5: Monitoreo y Reportes */}
              <Route path="/monitoreo" element={<Monitoreo />} />
              {/* P6: Auditoría y Trazabilidad */}
              <Route path="/bitacora" element={<Bitacora />} />
            </Route>
          </Routes>
        </Suspense>
      </AuthProvider>
    </BrowserRouter>
  );
}

const rootEl = document.getElementById('root');
if (rootEl) {
  ReactDOM.createRoot(rootEl).render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
}
