import React, { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import Dashboard from '../Dashboard';
import './Monitoreo.css';

const Monitoreo = () => {
  const location = useLocation();
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('dashboard');

  // Componentes disponibles en el paquete
  const componentes = [
    {
      id: 'dashboard',
      name: 'CU16 - Visualizar Dashboard',
      description: 'Indicadores de planificación, asistencia y conflictos',
      icon: '📊',
      component: Dashboard,
      roles: ['Administrador', 'Coordinador Académico']
    }
  ];

  // Filtrar componentes según el rol del usuario
  const componentesDisponibles = componentes.filter(comp => 
    comp.roles.includes(user?.rol?.nombre)
  );

  // Obtener componente activo
  const componenteActual = componentes.find(c => c.id === activeTab);
  const ComponenteActual = componenteActual?.component;

  return (
    <div className="monitoreo-container">
      <div className="monitoreo-header">
        <div className="monitoreo-title">
          <h1>Monitoreo y Reportes</h1>
          <p>Paquete P5 - Indicadores y análisis de procesos académicos</p>
        </div>
      </div>

      <div className="monitoreo-content">
        {/* Menú de componentes */}
        <div className="monitoreo-menu">
          <div className="menu-tabs">
            {componentesDisponibles.map((comp) => (
              <button
                key={comp.id}
                className={`menu-tab ${activeTab === comp.id ? 'active' : ''}`}
                onClick={() => setActiveTab(comp.id)}
                title={comp.description}
              >
                <span className="tab-icon">{comp.icon}</span>
                <span className="tab-name">{comp.name}</span>
              </button>
            ))}
          </div>

          {/* Información del componente seleccionado */}
          <div className="menu-info">
            <p className="info-description">
              {componenteActual?.description}
            </p>
          </div>
        </div>

        {/* Contenedor del componente actual */}
        <div className="monitoreo-view">
          {ComponenteActual ? (
            <ComponenteActual />
          ) : (
            <div className="no-access">
              <p>No tienes acceso a este componente</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Monitoreo;
