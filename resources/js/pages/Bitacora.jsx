import React, { useState, useEffect } from 'react';
import axios from 'axios';
import './Bitacora.css';

const Bitacora = () => {
    const [bitacoras, setBitacoras] = useState([]);
    const [loading, setLoading] = useState(false);
    const [pagination, setPagination] = useState({});
    const [filtros, setFiltros] = useState({
        usuario: '',
        modulo: '',
        accion: '',
        fecha_desde: '',
        fecha_hasta: '',
        buscar: '',
        per_page: 50
    });
    const [modulos, setModulos] = useState([]);
    const [acciones, setAcciones] = useState([]);
    const [detalleModal, setDetalleModal] = useState(null);
    const [estadisticas, setEstadisticas] = useState(null);

    // Cargar datos iniciales
    useEffect(() => {
        cargarBitacora();
        cargarModulos();
        cargarAcciones();
        cargarEstadisticas();
    }, []);

    // Cargar bitácora con filtros
    const cargarBitacora = async (page = 1) => {
        setLoading(true);
        try {
            const params = { ...filtros, page };
            const response = await axios.get('/api/bitacora', { params });
            
            if (response.data.success) {
                setBitacoras(response.data.data);
                setPagination(response.data.pagination);
            }
        } catch (error) {
            console.error('Error al cargar bitácora:', error);
        } finally {
            setLoading(false);
        }
    };

    // Cargar módulos únicos
    const cargarModulos = async () => {
        try {
            const response = await axios.get('/api/bitacora/modulos');
            if (response.data.success) {
                setModulos(response.data.data);
            }
        } catch (error) {
            console.error('Error al cargar módulos:', error);
        }
    };

    // Cargar acciones únicas
    const cargarAcciones = async () => {
        try {
            const response = await axios.get('/api/bitacora/acciones');
            if (response.data.success) {
                setAcciones(response.data.data);
            }
        } catch (error) {
            console.error('Error al cargar acciones:', error);
        }
    };

    // Cargar estadísticas
    const cargarEstadisticas = async () => {
        try {
            const response = await axios.get('/api/bitacora/estadisticas');
            if (response.data.success) {
                setEstadisticas(response.data.data);
            }
        } catch (error) {
            console.error('Error al cargar estadísticas:', error);
        }
    };

    // Manejar cambios en filtros
    const handleFiltroChange = (e) => {
        const { name, value } = e.target;
        setFiltros(prev => ({
            ...prev,
            [name]: value
        }));
    };

    // Aplicar filtros
    const aplicarFiltros = () => {
        cargarBitacora(1);
    };

    // Limpiar filtros
    const limpiarFiltros = () => {
        setFiltros({
            usuario: '',
            modulo: '',
            accion: '',
            fecha_desde: '',
            fecha_hasta: '',
            buscar: '',
            per_page: 50
        });
    };

    // Ver detalles de un registro
    const verDetalles = async (id) => {
        try {
            const response = await axios.get(`/api/bitacora/${id}`);
            if (response.data.success) {
                setDetalleModal(response.data.data);
            }
        } catch (error) {
            console.error('Error al cargar detalles:', error);
        }
    };

    // Cerrar modal de detalles
    const cerrarModal = () => {
        setDetalleModal(null);
    };

    // Exportar a CSV
    const exportarCSV = async () => {
        try {
            const response = await axios.post('/api/bitacora/exportar-csv', filtros, {
                responseType: 'blob'
            });
            
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `bitacora_${new Date().toISOString().split('T')[0]}.csv`);
            document.body.appendChild(link);
            link.click();
            link.parentElement.removeChild(link);
        } catch (error) {
            console.error('Error al exportar:', error);
        }
    };

    const getEstiloBadge = (accion) => {
        if (accion.includes('CREATE') || accion.includes('Crear')) return 'badge-success';
        if (accion.includes('UPDATE') || accion.includes('Actualizar')) return 'badge-info';
        if (accion.includes('DELETE') || accion.includes('Eliminar')) return 'badge-danger';
        if (accion.includes('LOGIN') || accion.includes('Inicio')) return 'badge-primary';
        if (accion.includes('LOGOUT') || accion.includes('Cierre')) return 'badge-secondary';
        return 'badge-warning';
    };

    return (
        <div className="bitacora-container">
            <div className="bitacora-header">
                <h1>CU18 - Registrar Bitácora de Acciones</h1>
                <p className="subtitle">Auditoría y Trazabilidad del Sistema</p>
            </div>

            {/* Estadísticas */}
            {estadisticas && (
                <div className="estadisticas-grid">
                    <div className="stat-card">
                        <div className="stat-value">{estadisticas.total_acciones}</div>
                        <div className="stat-label">Total de Acciones</div>
                    </div>
                    <div className="stat-card">
                        <div className="stat-value">{estadisticas.acciones_hoy}</div>
                        <div className="stat-label">Acciones Hoy</div>
                    </div>
                    <div className="stat-card">
                        <div className="stat-value">{estadisticas.acciones_semana}</div>
                        <div className="stat-label">Esta Semana</div>
                    </div>
                    <div className="stat-card">
                        <div className="stat-value">{estadisticas.usuarios_activos_hoy}</div>
                        <div className="stat-label">Usuarios Activos Hoy</div>
                    </div>
                </div>
            )}

            {/* Filtros */}
            <div className="filtros-section">
                <h3>Filtros</h3>
                <div className="filtros-grid">
                    <div className="filtro-grupo">
                        <label>Búsqueda General</label>
                        <input
                            type="text"
                            name="buscar"
                            value={filtros.buscar}
                            onChange={handleFiltroChange}
                            placeholder="Buscar en descripción y detalles..."
                            className="input-filtro"
                        />
                    </div>

                    <div className="filtro-grupo">
                        <label>Módulo</label>
                        <select
                            name="modulo"
                            value={filtros.modulo}
                            onChange={handleFiltroChange}
                            className="input-filtro"
                        >
                            <option value="">-- Todos los módulos --</option>
                            {modulos.map((mod, idx) => (
                                <option key={idx} value={mod}>{mod}</option>
                            ))}
                        </select>
                    </div>

                    <div className="filtro-grupo">
                        <label>Acción</label>
                        <select
                            name="accion"
                            value={filtros.accion}
                            onChange={handleFiltroChange}
                            className="input-filtro"
                        >
                            <option value="">-- Todas las acciones --</option>
                            {acciones.map((acc, idx) => (
                                <option key={idx} value={acc}>{acc}</option>
                            ))}
                        </select>
                    </div>

                    <div className="filtro-grupo">
                        <label>Desde Fecha</label>
                        <input
                            type="date"
                            name="fecha_desde"
                            value={filtros.fecha_desde}
                            onChange={handleFiltroChange}
                            className="input-filtro"
                        />
                    </div>

                    <div className="filtro-grupo">
                        <label>Hasta Fecha</label>
                        <input
                            type="date"
                            name="fecha_hasta"
                            value={filtros.fecha_hasta}
                            onChange={handleFiltroChange}
                            className="input-filtro"
                        />
                    </div>

                    <div className="filtro-grupo">
                        <label>Registros por página</label>
                        <select
                            name="per_page"
                            value={filtros.per_page}
                            onChange={handleFiltroChange}
                            className="input-filtro"
                        >
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                        </select>
                    </div>
                </div>

                <div className="filtros-acciones">
                    <button onClick={aplicarFiltros} className="btn btn-primary" disabled={loading}>
                        {loading ? 'Cargando...' : 'Aplicar Filtros'}
                    </button>
                    <button onClick={limpiarFiltros} className="btn btn-secondary">
                        Limpiar Filtros
                    </button>
                    <button onClick={exportarCSV} className="btn btn-success">
                        📥 Exportar CSV
                    </button>
                </div>
            </div>

            {/* Tabla de bitácora */}
            <div className="bitacora-table-container">
                <table className="bitacora-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>Fecha y Hora</th>
                            <th>IP Address</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {bitacoras.length > 0 ? (
                            bitacoras.map((bitacora) => (
                                <tr key={bitacora.id_bit} className="bitacora-row">
                                    <td>
                                        <span className="usuario-badge">
                                            {bitacora.usuario?.nombre_usuario || 'Sistema'}
                                        </span>
                                    </td>
                                    <td>
                                        <span className="modulo-badge">{bitacora.modulo}</span>
                                    </td>
                                    <td>
                                        <span className={`badge ${getEstiloBadge(bitacora.accion)}`}>
                                            {bitacora.accion}
                                        </span>
                                    </td>
                                    <td className="desc-cell">
                                        <span title={bitacora.descripcion}>
                                            {bitacora.descripcion?.substring(0, 50)}
                                            {bitacora.descripcion?.length > 50 ? '...' : ''}
                                        </span>
                                    </td>
                                    <td className="fecha-cell">
                                        {new Date(bitacora.fecha_accion).toLocaleString('es-ES')}
                                    </td>
                                    <td className="ip-cell">
                                        <code>{bitacora.ip_address || 'N/A'}</code>
                                    </td>
                                    <td className="acciones-cell">
                                        <button
                                            onClick={() => verDetalles(bitacora.id_bit)}
                                            className="btn-detalles"
                                            title="Ver detalles"
                                        >
                                            👁️
                                        </button>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan="7" className="sin-datos">
                                    No hay registros que coincidan con los filtros
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Paginación */}
            {pagination.last_page > 1 && (
                <div className="paginacion">
                    <button
                        onClick={() => cargarBitacora(1)}
                        disabled={pagination.current_page === 1}
                        className="btn btn-sm"
                    >
                        ⏮️
                    </button>
                    <button
                        onClick={() => cargarBitacora(pagination.current_page - 1)}
                        disabled={pagination.current_page === 1}
                        className="btn btn-sm"
                    >
                        ◀️
                    </button>

                    <span className="pagina-info">
                        Página {pagination.current_page} de {pagination.last_page}
                        ({pagination.from}-{pagination.to} de {pagination.total})
                    </span>

                    <button
                        onClick={() => cargarBitacora(pagination.current_page + 1)}
                        disabled={pagination.current_page === pagination.last_page}
                        className="btn btn-sm"
                    >
                        ▶️
                    </button>
                    <button
                        onClick={() => cargarBitacora(pagination.last_page)}
                        disabled={pagination.current_page === pagination.last_page}
                        className="btn btn-sm"
                    >
                        ⏭️
                    </button>
                </div>
            )}

            {/* Modal de Detalles */}
            {detalleModal && (
                <div className="modal-overlay" onClick={cerrarModal}>
                    <div className="modal-content" onClick={(e) => e.stopPropagation()}>
                        <div className="modal-header">
                            <h3>Detalles de Acción</h3>
                            <button onClick={cerrarModal} className="btn-close">✕</button>
                        </div>
                        <div className="modal-body">
                            <div className="detalle-grid">
                                <div className="detalle-item">
                                    <span className="label">ID:</span>
                                    <span className="valor">{detalleModal.id_bit}</span>
                                </div>
                                <div className="detalle-item">
                                    <span className="label">Usuario:</span>
                                    <span className="valor">{detalleModal.usuario_nombre}</span>
                                </div>
                                <div className="detalle-item">
                                    <span className="label">Módulo:</span>
                                    <span className="valor">{detalleModal.modulo}</span>
                                </div>
                                <div className="detalle-item">
                                    <span className="label">Acción:</span>
                                    <span className="valor">{detalleModal.accion}</span>
                                </div>
                                <div className="detalle-item">
                                    <span className="label">Fecha y Hora:</span>
                                    <span className="valor">{detalleModal.fecha_accion_formateada}</span>
                                </div>
                                <div className="detalle-item">
                                    <span className="label">IP Address:</span>
                                    <span className="valor"><code>{detalleModal.ip_address || 'N/A'}</code></span>
                                </div>
                                <div className="detalle-item">
                                    <span className="label">Tabla Afectada:</span>
                                    <span className="valor">{detalleModal.tabla_afectada || 'N/A'}</span>
                                </div>
                                <div className="detalle-item">
                                    <span className="label">Registro ID:</span>
                                    <span className="valor">{detalleModal.registro_id || 'N/A'}</span>
                                </div>
                                <div className="detalle-item full-width">
                                    <span className="label">Descripción:</span>
                                    <span className="valor">{detalleModal.descripcion}</span>
                                </div>
                                <div className="detalle-item full-width">
                                    <span className="label">User Agent:</span>
                                    <span className="valor small-text">{detalleModal.user_agent || 'N/A'}</span>
                                </div>
                                {detalleModal.detalles_json && Object.keys(detalleModal.detalles_json).length > 0 && (
                                    <div className="detalle-item full-width">
                                        <span className="label">Detalles Adicionales:</span>
                                        <pre className="valor-json">
                                            {JSON.stringify(detalleModal.detalles_json, null, 2)}
                                        </pre>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Bitacora;
