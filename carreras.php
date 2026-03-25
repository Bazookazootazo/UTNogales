<?php $pagina_actual = 'carreras'; ?>
<?php
session_start();
include 'config/conexion.php'; 
if (!isset($_SESSION['id_usuario'])) {
    header("Location: registro.php");
    exit();
}

$id_logueado = $_SESSION['id_usuario'];
try {
    $query_user = "SELECT nombreUser, apellidosUser, rol FROM usuarios WHERE numeroUser = ?";
    $stmt = $conn->prepare($query_user);
    $stmt->execute([$id_logueado]);
    $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($datos_usuario) {
        $nombre_completo = $datos_usuario['nombreUser'] . " " . $datos_usuario['apellidosUser'];
        $rol = $datos_usuario['rol'];
        $n = mb_substr($datos_usuario['nombreUser'], 0, 1);
        $a = mb_substr($datos_usuario['apellidosUser'], 0, 1);
        $iniciales = strtoupper($n . $a);
    } else {
        session_destroy();
        header("Location: registro.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
$id_check = $_SESSION['id_usuario'];
    
    $stmt_check = $conn->prepare("SELECT estatus FROM usuarios WHERE numeroUser = ?");
    $stmt_check->execute([$id_check]);
    $user_status = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$user_status || strtoupper($user_status['estatus']) === 'Inactivo') {
        session_unset();
        session_destroy();
        header("Location: index.php?error=" . urlencode("Tu sesión ha expirado o tu cuenta ha sido desactivada."));
        exit();
    }
?>

<?php include_once 'includes/header_sidebar.php'; ?>

    <!-- ════════════════════════════════════════════════
         CONTENIDO PRINCIPAL
    ════════════════════════════════════════════════ -->
    <div class="mtb-content">

        <!-- ── TOP BAR ── -->
        <header class="mtb-topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="toggleSidebar" aria-label="Abrir menú">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="topbar-title">Carreras</div>
                    <div class="topbar-breadcrumb">Carreras › Resumen general</div>
                </div>
            </div>
            <div class="topbar-right">
                <button class="topbar-action-btn" onclick="abrirModalInscripcion()">
                    <i class="fas fa-plus"></i> Registrar nueva carrera
                </button>
                <button class="topbar-icon-btn" title="Notificaciones">
                    <i class="fas fa-bell"></i>
                    <span class="topbar-badge"></span>
                </button>
                <button class="topbar-icon-btn" title="Configuración">
                    <i class="fas fa-gear"></i>
                </button>
            </div>
        </header>

        <!-- ── INNER / PÁGINA ACTUAL ── -->
        <main class="mtb-inner">

            <!-- PAGE HEADER -->
            <div class="page-header">
                <div class="page-header-info">
                    <h1>Resumen General del Sistema</h1>
                    <p>Temporada 2026 · Actualizado hoy</p>
                </div>
                <div class="page-header-actions">
                    <button class="btn btn-secondary btn-sm">
                        <i class="fas fa-file-export"></i> Exportar
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="abrirModalEvento()">
                        <i class="fas fa-calendar-plus"></i> Nuevo Evento
                    </button>
                </div>
            </div>

            <!-- ── KPI CARDS ── -->
            <div class="stats-grid">
                <div class="stat-card animate-in">
                    <div class="stat-card-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-card-body">
                        <div class="stat-card-value">12</div>
                        <div class="stat-card-label">Eventos en temporada</div>
                    </div>
                    <div class="stat-card-trend up">
                        <i class="fas fa-arrow-up"></i> +2 vs. año anterior
                    </div>
                </div>

                <div class="stat-card success animate-in">
                    <div class="stat-card-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-card-body">
                        <div class="stat-card-value">348</div>
                        <div class="stat-card-label">Deportistas activos</div>
                    </div>
                    <div class="stat-card-trend up">
                        <i class="fas fa-arrow-up"></i> +47 nuevos
                    </div>
                </div>

                <div class="stat-card warning animate-in">
                    <div class="stat-card-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div class="stat-card-body">
                        <div class="stat-card-value">94</div>
                        <div class="stat-card-label">Inscripciones pendientes</div>
                    </div>
                    <div class="stat-card-trend down">
                        <i class="fas fa-arrow-down"></i> 3 vencidas
                    </div>
                </div>

                <div class="stat-card info animate-in">
                    <div class="stat-card-icon"><i class="fas fa-map-location-dot"></i></div>
                    <div class="stat-card-body">
                        <div class="stat-card-value">7</div>
                        <div class="stat-card-label">Pistas registradas</div>
                    </div>
                    <div class="stat-card-trend up">
                        <i class="fas fa-arrow-up"></i> 1 nueva este mes
                    </div>
                </div>
            </div>

            <!-- ── FILA: PRÓXIMOS EVENTOS + RANKING ── -->
            <div style="display:grid; grid-template-columns:1fr 380px; gap:24px; margin-bottom:24px;">

                <!-- PRÓXIMOS EVENTOS -->
                <div class="card animate-in">
                    <div class="card-header">
                        <div>
                            <div class="card-title">
                                <i class="fas fa-calendar-days"></i> Próximos Eventos
                            </div>
                            <div class="card-subtitle">Calendario de la temporada 2026</div>
                        </div>
                        <button class="btn btn-outline btn-sm">Ver todos</button>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <table class="mtb-table">
                            <thead>
                                <tr>
                                    <th>Evento</th>
                                    <th>Pista</th>
                                    <th>Fecha</th>
                                    <th>Inscritos</th>
                                    <th>Estatus</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyEventos"><!-- Generado por JS --></tbody>
                        </table>
                    </div>
                </div>

                <!-- RANKING TOP 5 -->
                <div class="card animate-in">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-trophy"></i> Top 5 Temporada
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="rankingList"
                             style="display:flex; flex-direction:column; gap:12px;">
                            <!-- Generado por JS -->
                        </div>
                    </div>
                    <div class="card-footer">
                        <span style="font-size:.8rem; color:var(--mtb-gray-600);">
                            Actualizado: hoy
                        </span>
                        <button class="btn btn-ghost btn-sm">
                            Ver ranking completo <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

            </div>
            <!-- FIN FILA EVENTOS + RANKING -->

            <!-- ── TABLA DE INSCRIPCIONES ── -->
            <div class="card animate-in">
                <div class="card-header">
                    <div>
                        <div class="card-title">
                            <i class="fas fa-clipboard-list"></i> Control de Inscripciones
                        </div>
                        <div class="card-subtitle">Gestión de participantes por evento</div>
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="abrirModalInscripcion()">
                        <i class="fas fa-plus"></i> Nueva
                    </button>
                </div>

                <!-- Filtros -->
                <div style="padding:0 24px;">
                    <div class="filters-bar"
                         style="margin-bottom:0; border-radius:var(--radius-md);">
                        <div class="filters-header">
                            <i class="fas fa-filter"></i> Filtrar Inscripciones
                        </div>
                        <div class="filters-row">
                            <div class="filter-field">
                                <label>Evento</label>
                                <select id="filtroEvento" onchange="aplicarFiltros()">
                                    <option value="">Todos los eventos</option>
                                    <option>Enduro Nogales</option>
                                    <option>XCO Hermosillo</option>
                                    <option>DH Sierra</option>
                                </select>
                            </div>
                            <div class="filter-field">
                                <label>Categoría</label>
                                <select id="filtroCategoria" onchange="aplicarFiltros()">
                                    <option value="">Todas</option>
                                    <option>Elite</option>
                                    <option>Sub-23</option>
                                    <option>Master</option>
                                    <option>Junior</option>
                                </select>
                            </div>
                            <div class="filter-field">
                                <label>Estatus</label>
                                <select id="filtroEstatus" onchange="aplicarFiltros()">
                                    <option value="">Todos</option>
                                    <option>Confirmado</option>
                                    <option>Pendiente</option>
                                    <option>Cancelado</option>
                                </select>
                            </div>
                            <div class="filter-field autocomplete-wrapper">
                                <label>Buscar deportista</label>
                                <div class="input-group">
                                    <span class="input-group-icon">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" id="buscarDeportista"
                                           class="form-control"
                                           placeholder="Nombre o número..."
                                           oninput="aplicarFiltros()">
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button class="btn btn-secondary btn-sm"
                                        onclick="limpiarFiltros()">
                                    <i class="fas fa-xmark"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="card-body" style="padding-top:16px;">
                    <div class="table-wrapper">
                        <table class="mtb-table" id="tablaInscripciones">
                            <thead>
                                <tr>
                                    <th class="sortable" data-col="0">#</th>
                                    <th class="sortable" data-col="1">Deportista</th>
                                    <th class="sortable" data-col="2">Evento</th>
                                    <th class="sortable" data-col="3">Categoría</th>
                                    <th class="sortable" data-col="4">Pista</th>
                                    <th class="sortable" data-col="5">Fecha</th>
                                    <th class="sortable" data-col="6">Estatus</th>
                                    <th class="center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyInscripciones"><!-- Generado por JS --></tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="pagination-bar" id="paginacionBar">
                        <div class="d-flex align-center gap-md">
                            <span class="pagination-info" id="paginacionInfo">
                                Mostrando 1–10 de 24 inscripciones
                            </span>
                            <select class="page-size-select" id="pageSize"
                                    onchange="cambiarTamano()">
                                <option value="10" selected>10 / pág.</option>
                                <option value="25">25 / pág.</option>
                                <option value="50">50 / pág.</option>
                            </select>
                        </div>
                        <div class="pagination-controls" id="paginacionControls">
                            <!-- Generado por JS -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- FIN TABLA INSCRIPCIONES -->

        </main>
    </div>
    <!-- FIN CONTENT -->

</div>
<!-- FIN APP -->


<!-- ══════════════════════════════════════════════════════════
     MODAL: NUEVA INSCRIPCIÓN
══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalInscripcion">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h2><i class="fas fa-clipboard-list"></i> Nueva Inscripción</h2>
            <button class="modal-close" onclick="cerrarModal('modalInscripcion')">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formInscripcion" onsubmit="guardarInscripcion(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Deportista</label>
                        <div class="input-group">
                            <span class="input-group-icon"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" name="deportista"
                                   placeholder="Nombre completo" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Número de Dorsal</label>
                        <input type="number" class="form-control" name="dorsal"
                               placeholder="Ej. 42" min="1" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Evento</label>
                        <select class="form-control" name="evento" required>
                            <option value="">Seleccione evento...</option>
                            <option>Enduro Nogales</option>
                            <option>XCO Hermosillo</option>
                            <option>DH Sierra Madre</option>
                            <option>Cross Country Ures</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Categoría</label>
                        <select class="form-control" name="categoria" required>
                            <option value="">Seleccione...</option>
                            <option>Elite</option>
                            <option>Sub-23</option>
                            <option>Master A (30-39)</option>
                            <option>Master B (40-49)</option>
                            <option>Junior</option>
                            <option>Femenil Elite</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Pista</label>
                        <select class="form-control" name="pista" required>
                            <option value="">Seleccione pista...</option>
                            <option>La Rumorosa — Baja California</option>
                            <option>Cerro de la Silla — NL</option>
                            <option>Sierra Fría — Aguascalientes</option>
                            <option>Monte Albán — Oaxaca</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Fecha de inscripción</label>
                        <input type="date" class="form-control" name="fecha" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Notas / Observaciones</label>
                    <textarea class="form-control" name="notas"
                              placeholder="Información adicional del participante..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal('modalInscripcion')">
                <i class="fas fa-xmark"></i> Cancelar
            </button>
            <button class="btn btn-primary" onclick="guardarInscripcion(event)">
                <i class="fas fa-save"></i> Guardar Inscripción
            </button>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════
     MODAL: NUEVO EVENTO
══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalEvento">
    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-calendar-plus"></i> Nuevo Evento</h2>
            <button class="modal-close" onclick="cerrarModal('modalEvento')">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formEvento">
                <div class="form-group">
                    <label class="form-label required">Nombre del Evento</label>
                    <input type="text" class="form-control"
                           placeholder="Ej. Enduro Copa MTB Nogales 2026" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Fecha del Evento</label>
                        <input type="date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cupo máximo</label>
                        <input type="number" class="form-control" placeholder="Ej. 200" min="1">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label required">Pista</label>
                    <select class="form-control" required>
                        <option value="">Seleccione pista...</option>
                        <option>La Rumorosa — Baja California</option>
                        <option>Cerro de la Silla — NL</option>
                        <option>Sierra Fría — Aguascalientes</option>
                        <option>Monte Albán — Oaxaca</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control"
                              placeholder="Modalidad, desnivel, distancia..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal('modalEvento')">
                Cancelar
            </button>
            <button class="btn btn-primary"
                    onclick="cerrarModal('modalEvento'); showToast('Evento creado correctamente','success')">
                <i class="fas fa-save"></i> Crear Evento
            </button>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════
     MODAL: DETALLE DE INSCRIPCIÓN
══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalDetalle">
    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-id-card"></i> Detalle de Inscripción</h2>
            <button class="modal-close" onclick="cerrarModal('modalDetalle')">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body" id="modalDetalleBody">
            <!-- Contenido inyectado por JS -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal('modalDetalle')">
                Cerrar
            </button>
            <button class="btn btn-warning btn-sm">
                <i class="fas fa-pen"></i> Editar
            </button>
        </div>
    </div>
</div>


<!-- ── TOAST CONTAINER ── -->
<div class="toast-container" id="toastContainer"></div>


<!-- ══════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* ────────────────────────────────────────────────────────────
   DATOS MOCK — En producción, estos vendrían del backend (PHP/AJAX)
──────────────────────────────────────────────────────────── */
const DATA_EVENTOS = [
    { nombre: 'Enduro Nogales',     pista: 'La Rumorosa',        fecha: '2026-04-05', inscritos: 78,  estatus: 'Abierto' },
    { nombre: 'XCO Hermosillo',     pista: 'Cerro de la Silla',  fecha: '2026-04-19', inscritos: 112, estatus: 'Abierto' },
    { nombre: 'DH Sierra Madre',    pista: 'Sierra Fría',        fecha: '2026-05-10', inscritos: 55,  estatus: 'Próximo' },
    { nombre: 'Cross Country Ures', pista: 'Monte Albán',        fecha: '2026-05-24', inscritos: 30,  estatus: 'Próximo' },
    { nombre: 'Marathon Alamos',    pista: 'Alamos Trail',       fecha: '2026-06-07', inscritos: 14,  estatus: 'Convocatoria' },
];

const DATA_RANKING = [
    { pos: 1, nombre: 'Carlos Mendoza',  puntos: 480, equipo: 'Trek Racing MX' },
    { pos: 2, nombre: 'Sofía Gutiérrez', puntos: 440, equipo: 'Canyon Women' },
    { pos: 3, nombre: 'Roberto Vega',    puntos: 395, equipo: 'Specialized BC' },
    { pos: 4, nombre: 'Ana Torres',      puntos: 360, equipo: 'Giant Team NL' },
    { pos: 5, nombre: 'Luis Herrera',    puntos: 310, equipo: 'Trek Racing MX' },
];

/* ────────────────────────────────────────────────────────────
   ESTADO DE LA PAGINACIÓN
──────────────────────────────────────────────────────────── */
let paginaActual = 1;
let tamPagina    = 10;
let datosFiltrados = [...DATA_INSCRIPCIONES];

/* ────────────────────────────────────────────────────────────
   RENDER: TABLA DE EVENTOS
──────────────────────────────────────────────────────────── */
function renderEventos() {
    const estatusClase = { 'Abierto': 'badge-success', 'Próximo': 'badge-info', 'Convocatoria': 'badge-warning' };
    const tbody = document.getElementById('tbodyEventos');
    tbody.innerHTML = DATA_EVENTOS.map(e => `
        <tr>
            <td><strong>${e.nombre}</strong></td>
            <td><i class="fas fa-map-pin" style="color:var(--mtb-primary);margin-right:4px;"></i>${e.pista}</td>
            <td>${formatFecha(e.fecha)}</td>
            <td><span style="font-weight:700;">${e.inscritos}</span> <span style="color:var(--mtb-gray-500);font-size:.8rem;">/ cupo</span></td>
            <td><span class="badge ${estatusClase[e.estatus] || 'badge-dark'}"><span class="dot"></span>${e.estatus}</span></td>
        </tr>
    `).join('');
}

/* ────────────────────────────────────────────────────────────
   RENDER: RANKING
──────────────────────────────────────────────────────────── */
function renderRanking() {
    const medallas = ['🥇','🥈','🥉'];
    const list = document.getElementById('rankingList');
    list.innerHTML = DATA_RANKING.map(r => `
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-size:1.3rem;width:28px;text-align:center;">${medallas[r.pos-1] || r.pos}</span>
            <div class="avatar avatar-sm">${r.nombre.split(' ').map(n=>n[0]).join('').slice(0,2)}</div>
            <div style="flex:1;">
                <div style="font-weight:700;font-size:.875rem;color:var(--mtb-dark);">${r.nombre}</div>
                <div style="font-size:.75rem;color:var(--mtb-gray-600);">${r.equipo}</div>
            </div>
            <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--mtb-primary);">${r.puntos} <span style="font-size:.65rem;font-weight:400;color:var(--mtb-gray-500);">pts</span></div>
        </div>
    `).join('');
}

/* ────────────────────────────────────────────────────────────
   RENDER: TABLA DE INSCRIPCIONES (con paginación)
──────────────────────────────────────────────────────────── */
function renderInscripciones() {
    const inicio = (paginaActual - 1) * tamPagina;
    const fin    = inicio + tamPagina;
    const pagina = datosFiltrados.slice(inicio, fin);

    const estatusBadge = {
        'Confirmado': 'badge-success',
        'Pendiente':  'badge-warning',
        'Cancelado':  'badge-danger',
    };

    const tbody = document.getElementById('tbodyInscripciones');

    if (pagina.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="table-empty"><i class="fas fa-inbox"></i>Sin resultados para los filtros aplicados</td></tr>`;
    } else {
        tbody.innerHTML = pagina.map(ins => `
            <tr>
                <td><strong>#${ins.dorsal}</strong></td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div class="avatar avatar-sm">${ins.deportista.split(' ').map(n=>n[0]).join('').slice(0,2)}</div>
                        <span style="font-weight:600;">${ins.deportista}</span>
                    </div>
                </td>
                <td>${ins.evento}</td>
                <td><span class="badge badge-dark">${ins.categoria}</span></td>
                <td><i class="fas fa-map-pin" style="color:var(--mtb-primary);margin-right:4px;font-size:.75rem;"></i>${ins.pista}</td>
                <td style="color:var(--mtb-gray-600);font-size:.85rem;">${formatFecha(ins.fecha)}</td>
                <td><span class="badge ${estatusBadge[ins.estatus] || 'badge-dark'}"><span class="dot"></span>${ins.estatus}</span></td>
                <td class="center">
                    <div style="display:flex;gap:4px;justify-content:center;">
                        <button class="btn btn-ghost btn-sm" title="Ver detalle" onclick="verDetalle(${ins.dorsal})"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-ghost btn-sm" title="Editar" style="color:var(--mtb-info)"><i class="fas fa-pen"></i></button>
                        <button class="btn btn-ghost btn-sm" title="Eliminar" style="color:var(--mtb-danger)" onclick="eliminarFila(${ins.dorsal})"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderPaginacion();
}

/* ────────────────────────────────────────────────────────────
   RENDER: PAGINACIÓN
──────────────────────────────────────────────────────────── */
function renderPaginacion() {
    const total   = datosFiltrados.length;
    const totalPag = Math.ceil(total / tamPagina);
    const inicio  = total === 0 ? 0 : (paginaActual - 1) * tamPagina + 1;
    const fin     = Math.min(paginaActual * tamPagina, total);

    document.getElementById('paginacionInfo').textContent =
        `Mostrando ${inicio}–${fin} de ${total} inscripciones`;

    const container = document.getElementById('paginacionControls');
    let html = '';

    // Anterior
    html += `<button class="page-btn" onclick="irPagina(${paginaActual-1})" ${paginaActual===1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;

    // Números
    const desde = Math.max(1, paginaActual - 2);
    const hasta = Math.min(totalPag, paginaActual + 2);
    if (desde > 1) html += `<button class="page-btn" onclick="irPagina(1)">1</button>${desde > 2 ? '<span style="padding:0 4px;color:var(--mtb-gray-500)">…</span>' : ''}`;
    for (let i = desde; i <= hasta; i++) {
        html += `<button class="page-btn ${i===paginaActual?'active':''}" onclick="irPagina(${i})">${i}</button>`;
    }
    if (hasta < totalPag) html += `${hasta < totalPag-1 ? '<span style="padding:0 4px;color:var(--mtb-gray-500)">…</span>' : ''}<button class="page-btn" onclick="irPagina(${totalPag})">${totalPag}</button>`;

    // Siguiente
    html += `<button class="page-btn" onclick="irPagina(${paginaActual+1})" ${paginaActual===totalPag||totalPag===0?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;

    container.innerHTML = html;
}

function irPagina(n) {
    const total = Math.ceil(datosFiltrados.length / tamPagina);
    if (n < 1 || n > total) return;
    paginaActual = n;
    renderInscripciones();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cambiarTamano() {
    tamPagina = parseInt(document.getElementById('pageSize').value);
    paginaActual = 1;
    renderInscripciones();
}

/* ────────────────────────────────────────────────────────────
   FILTROS
──────────────────────────────────────────────────────────── */
function aplicarFiltros() {
    const filtroEvento    = document.getElementById('filtroEvento').value.toLowerCase();
    const filtroCategoria = document.getElementById('filtroCategoria').value.toLowerCase();
    const filtroEstatus   = document.getElementById('filtroEstatus').value.toLowerCase();
    const busqueda        = document.getElementById('buscarDeportista').value.toLowerCase().trim();

    datosFiltrados = DATA_INSCRIPCIONES.filter(ins => {
        const coincideEvento    = !filtroEvento    || ins.evento.toLowerCase().includes(filtroEvento);
        const coincideCategoria = !filtroCategoria || ins.categoria.toLowerCase() === filtroCategoria;
        const coincideEstatus   = !filtroEstatus   || ins.estatus.toLowerCase() === filtroEstatus;
        const coincideBusqueda  = !busqueda        || ins.deportista.toLowerCase().includes(busqueda) || String(ins.dorsal).includes(busqueda);
        return coincideEvento && coincideCategoria && coincideEstatus && coincideBusqueda;
    });

    paginaActual = 1;
    renderInscripciones();
}

function limpiarFiltros() {
    document.getElementById('filtroEvento').value    = '';
    document.getElementById('filtroCategoria').value = '';
    document.getElementById('filtroEstatus').value   = '';
    document.getElementById('buscarDeportista').value = '';
    datosFiltrados = [...DATA_INSCRIPCIONES];
    paginaActual = 1;
    renderInscripciones();
}

/* ────────────────────────────────────────────────────────────
   MODALES
──────────────────────────────────────────────────────────── */
function abrirModal(id) {
    const overlay = document.getElementById(id);
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function cerrarModal(id) {
    const overlay = document.getElementById(id);
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

function abrirModalInscripcion() { abrirModal('modalInscripcion'); }
function abrirModalEvento()      { abrirModal('modalEvento'); }

// Cerrar modal al hacer clic en el overlay
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) cerrarModal(this.id);
    });
});

// Guardar inscripción (demo)
function guardarInscripcion(e) {
    if (e) e.preventDefault();
    cerrarModal('modalInscripcion');
    showToast('Inscripción guardada correctamente', 'success');
}

/* ────────────────────────────────────────────────────────────
   DETALLE
──────────────────────────────────────────────────────────── */
function verDetalle(dorsal) {
    const ins = DATA_INSCRIPCIONES.find(i => i.dorsal === dorsal);
    if (!ins) return;

    const estatusBadge = { 'Confirmado': 'badge-success', 'Pendiente': 'badge-warning', 'Cancelado': 'badge-danger' };

    document.getElementById('modalDetalleBody').innerHTML = `
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
            <div class="avatar avatar-lg">${ins.deportista.split(' ').map(n=>n[0]).join('').slice(0,2)}</div>
            <div>
                <div style="font-family:var(--font-display);font-size:1.4rem;font-weight:800;color:var(--mtb-dark);">${ins.deportista}</div>
                <div style="color:var(--mtb-gray-600);font-size:.875rem;">Dorsal #${ins.dorsal} · ${ins.categoria}</div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="card" style="padding:16px;">
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--mtb-gray-500);letter-spacing:.5px;margin-bottom:4px;">Evento</div>
                <div style="font-weight:700;">${ins.evento}</div>
            </div>
            <div class="card" style="padding:16px;">
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--mtb-gray-500);letter-spacing:.5px;margin-bottom:4px;">Pista</div>
                <div style="font-weight:700;">${ins.pista}</div>
            </div>
            <div class="card" style="padding:16px;">
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--mtb-gray-500);letter-spacing:.5px;margin-bottom:4px;">Fecha</div>
                <div style="font-weight:700;">${formatFecha(ins.fecha)}</div>
            </div>
            <div class="card" style="padding:16px;">
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--mtb-gray-500);letter-spacing:.5px;margin-bottom:4px;">Estatus</div>
                <span class="badge ${estatusBadge[ins.estatus] || 'badge-dark'}">${ins.estatus}</span>
            </div>
        </div>
    `;
    abrirModal('modalDetalle');
}

/* ────────────────────────────────────────────────────────────
   ELIMINAR FILA (demo)
──────────────────────────────────────────────────────────── */
function eliminarFila(dorsal) {
    if (!confirm(`¿Eliminar inscripción #${dorsal}?`)) return;
    const idx = DATA_INSCRIPCIONES.findIndex(i => i.dorsal === dorsal);
    if (idx > -1) DATA_INSCRIPCIONES.splice(idx, 1);
    datosFiltrados = datosFiltrados.filter(i => i.dorsal !== dorsal);
    renderInscripciones();
    showToast('Inscripción eliminada', 'danger');
}

/* ────────────────────────────────────────────────────────────
   SIDEBAR RESPONSIVE
──────────────────────────────────────────────────────────── */
document.getElementById('toggleSidebar').addEventListener('click', function() {
    document.getElementById('mtbSidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
});

document.getElementById('sidebarOverlay').addEventListener('click', function() {
    document.getElementById('mtbSidebar').classList.remove('open');
    this.classList.remove('active');
});


/* ────────────────────────────────────────────────────────────
   TOAST
──────────────────────────────────────────────────────────── */
function showToast(msg, tipo = 'primary') {
    const iconos = { success: 'fa-circle-check', danger: 'fa-circle-xmark', warning: 'fa-triangle-exclamation', info: 'fa-circle-info', primary: 'fa-bell' };
    const colores = { success: 'var(--mtb-success)', danger: 'var(--mtb-danger)', warning: 'var(--mtb-warning)', info: 'var(--mtb-info)', primary: 'var(--mtb-primary)' };

    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.borderLeftColor = colores[tipo] || colores.primary;
    toast.innerHTML = `<i class="fas ${iconos[tipo] || iconos.primary}" style="color:${colores[tipo]};"></i><span>${msg}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(30px)';
        toast.style.transition = 'all .3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/* ────────────────────────────────────────────────────────────
   UTILIDADES
──────────────────────────────────────────────────────────── */
function formatFecha(iso) {
    const [y, m, d] = iso.split('-');
    const meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    return `${parseInt(d)} ${meses[parseInt(m)-1]} ${y}`;
}


document.addEventListener('DOMContentLoaded', function() {
    renderEventos();
    renderRanking();
    renderInscripciones();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg === 'ok') {
        Swal.fire({
            title: '¡Bienvenido a MTB nogales!',
            text: 'se ha iniciado sesion correctamente.',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'ok2'){
        Sawl.fire({
            title: '¡Cuenta creada exitosamente!',
            text: 'Bienvenido a MTB nogales, ahora puedes iniciar sesión con tus credenciales.',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'ok3'){
        
        Swal.fire({
       
            title: '¡Sesión cerrada!',
            text: 'Has salido de tu cuenta de forma segura. ¡Vuelve pronto!',
            icon: 'info',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'bienvenido_de_nuevo_ok')
    {
        Swal.fire({
            title: '¡Bienvenido de nuevo a MTB nogales!',
            text: 'Has reactivado tu cuenta nuevamente. ¡Esperamos y disfrutes tu estadia!',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }

 function limpiarURL() {
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
    }
});

function confirmarCierreSesion(event) {
    event.preventDefault(); 
    Swal.fire({
        title: '¿Seguro que quieres cerrar sesión?',
        text: "Tendrás que volver a ingresar tus credenciales.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E8630A',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'actions/cerrarSesion.php'; 
        }
    });
}
</script>
<script>
function checarEstatusVivo() {
    fetch('verificar_estatus.php')
        .then(response => response.json())
        .then(data => {
            if (data.activo === false) {
                window.location.href = 'index.php?error=Tu cuenta ha sido desactivada.';
            }
        })
        .catch(error => console.error('Error verificando sesión:', error));
}

// Ejecutar cada 5 segundos (5000 milisegundos)
setInterval(checarEstatusVivo, 5000);
</script>
</body>
</html>
