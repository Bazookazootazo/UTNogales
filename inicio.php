<?php 
$pagina_actual = 'inicio';
require_once 'config/auth.php'; 
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
                    <div class="topbar-title">Dashboard</div>
                    <div class="topbar-breadcrumb">Inicio › Resumen general</div>
                </div>
            </div>
            <div class="topbar-right">
                <button class="topbar-action-btn" onclick="abrirModalInscripcion()">
                    <i class="fas fa-plus"></i> Nueva Inscripción
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

<?php include 'includes/footer_scripts.php'; ?>