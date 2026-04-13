<?php $pagina_actual = 'inscripciones'; ?>
<?php
// ============================================================
//  MTB NOGALES — Sección de Inscripciones
//  Ruta: Inscripciones.php
// ============================================================
session_start();
include 'config/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: registro.php");
    exit();
}

$id_logueado = $_SESSION['id_usuario'];
try {
    $stmt = $conn->prepare("SELECT nombreUser, apellidosUser, rol FROM usuarios WHERE numeroUser = ?");
    $stmt->execute([$id_logueado]);
    $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($datos_usuario) {
        $nombre_completo = $datos_usuario['nombreUser'] . " " . $datos_usuario['apellidosUser'];
        $rol    = $datos_usuario['rol'];
        $n      = mb_substr($datos_usuario['nombreUser'], 0, 1);
        $a      = mb_substr($datos_usuario['apellidosUser'], 0, 1);
        $iniciales = strtoupper($n . $a);
    } else {
        session_destroy();
        header("Location: registro.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}

// Verificar estatus activo
$stmt_check = $conn->prepare("SELECT estatus FROM usuarios WHERE numeroUser = ?");
$stmt_check->execute([$id_logueado]);
$user_status = $stmt_check->fetch(PDO::FETCH_ASSOC);
if (!$user_status || strtoupper($user_status['estatus']) === 'INACTIVO') {
    session_unset(); session_destroy();
    header("Location: index.php?error=" . urlencode("Tu sesión ha expirado o tu cuenta ha sido desactivada."));
    exit();
}

// ============================================================
//  CONSULTAS DE DATOS
// ============================================================
try {
    if ($rol === 'ADMIN') {
        // ADMIN: todas las inscripciones con detalle
        $stmt_ins = $conn->query("SELECT * FROM vw_inscripciones_admin ORDER BY fechaInscripcion DESC");
        $inscripciones = $stmt_ins->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // CICLISTA: solo sus propias inscripciones
        $stmt_ins = $conn->prepare("SELECT * FROM vw_inscripciones_ciclista WHERE numeroCiclista = ? ORDER BY fechaCarrera ASC");
        $stmt_ins->execute([$id_logueado]);
        $inscripciones = $stmt_ins->fetchAll(PDO::FETCH_ASSOC);
    }

    // Carreras disponibles para inscribirse (CICLISTA) o para filtrar (ADMIN)
    $stmt_carr = $conn->query("
        SELECT c.numeroCarrera, c.nombreCarrera, c.fechaCarrera, c.horaSalida,
               c.costoInscripcion, c.cupo, c.estatusCarrera, c.rutaImagen,
               p.nombrePista, p.seccion,
               (SELECT COUNT(*) FROM inscripciones WHERE numeroCarrera = c.numeroCarrera) AS totalInscritos,
               (SELECT GROUP_CONCAT(numeroCategoria) FROM carreras_categorias WHERE numeroCarrera = c.numeroCarrera) AS cats_ids
        FROM carreras c
        INNER JOIN pistas p ON c.numeroPista = p.numeroPista
        WHERE c.estatus = 'ACTIVO'
        ORDER BY c.fechaCarrera ASC
    ");
    $carreras_lista = $stmt_carr->fetchAll(PDO::FETCH_ASSOC);

    // Categorías
    $categorias = $conn->query("SELECT * FROM categorias ORDER BY edadMinima ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Estadísticas rápidas
    $total_ins     = count($inscripciones);
    $pendientes    = 0; $confirmados = 0; $rechazados = 0;
    foreach ($inscripciones as $i) {
        if ($i['estadoPago'] === 'PENDIENTE')   $pendientes++;
        if ($i['estadoPago'] === 'CONFIRMADO')  $confirmados++;
        if ($i['estadoPago'] === 'RECHAZADO')   $rechazados++;
    }

} catch (PDOException $e) {
    $inscripciones = []; $carreras_lista = []; $categorias = [];
    $total_ins = $pendientes = $confirmados = $rechazados = 0;
}

$categorias_json = json_encode($categorias, JSON_UNESCAPED_UNICODE);
$carreras_json   = json_encode($carreras_lista, JSON_UNESCAPED_UNICODE);
?>

<?php include_once 'includes/header_sidebar.php'; ?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripciones — MTB</title>
    <link rel="stylesheet" href="assets/css/mtb-dashboard.css">
    <style>
        /* ── Badges de pago ── */
        .badge-pago { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:var(--radius-full); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; }
        .pago-PENDIENTE  { background:var(--mtb-warning-bg);  color:#92400E; }
        .pago-CONFIRMADO { background:var(--mtb-success-bg);  color:#14532D; }
        .pago-RECHAZADO  { background:var(--mtb-danger-bg);   color:#7F1D1D; }
        .pago-EXENTO     { background:var(--mtb-info-bg);     color:#1E3A5F; }

        /* ── Cards de carrera para CICLISTA ── */
        .carreras-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(310px,1fr)); gap:20px; margin-top:20px; }
        .card-carrera  { background:#fff; border-radius:var(--radius-lg); overflow:hidden; box-shadow:var(--shadow-card); transition:transform var(--transition), box-shadow var(--transition); }
        .card-carrera:hover { transform:translateY(-3px); box-shadow:var(--shadow-md); }
        .card-carrera-img  { width:100%; height:160px; object-fit:cover; }
        .card-carrera-body { padding:16px; }
        .card-carrera-title { font-family:var(--font-display); font-size:1.15rem; font-weight:800; color:var(--mtb-dark); margin-bottom:6px; }
        .card-carrera-meta  { font-size:.8rem; color:var(--mtb-gray-600); display:flex; align-items:center; gap:5px; margin-bottom:4px; }
        .card-carrera-footer { padding:12px 16px; border-top:1px solid var(--mtb-gray-200); display:flex; align-items:center; justify-content:space-between; }

        /* ── Cupo progress ── */
        .cupo-bar { background:var(--mtb-gray-200); border-radius:var(--radius-full); height:6px; overflow:hidden; margin-top:6px; }
        .cupo-fill { height:100%; border-radius:var(--radius-full); background:linear-gradient(90deg,var(--mtb-primary),var(--mtb-primary-light)); transition:width .6s ease; }

        /* ── Tabla responsive ── */
        .tabla-wrap { overflow-x:auto; }

        /* ── Filtros ── */
        .filtros-bar { display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; margin-bottom:16px; }
        .filtros-bar .form-control { min-width:160px; font-size:.85rem; padding:7px 12px; }
        .filtros-bar .btn { white-space:nowrap; }

        /* ── Select categorías dentro del modal ── */
        .cat-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:10px; max-height:260px; overflow-y:auto; padding:4px 2px; }
        .cat-card { border:2px solid var(--mtb-gray-200); border-radius:var(--radius-md); padding:12px; cursor:pointer; transition:border-color .2s; }
        .cat-card:hover { border-color:var(--mtb-primary-light); }
        .cat-card.selected { border-color:var(--mtb-primary); background:var(--mtb-primary-bg); }
        .cat-card input { display:none; }
        .cat-card-name { font-weight:700; font-size:.9rem; color:var(--mtb-dark); }
        .cat-card-meta { font-size:.75rem; color:var(--mtb-gray-600); margin-top:2px; }

        /* ── Número corredor badge ── */
        .num-corredor { display:inline-block; background:var(--mtb-dark); color:#fff; font-family:var(--font-display); font-weight:800; font-size:1rem; width:36px; height:36px; line-height:36px; text-align:center; border-radius:50%; }
    </style>
</head>
<body>

<div class="mtb-content">

    <!-- ── TOP BAR ── -->
    <header class="mtb-topbar">
        <div class="topbar-left">
            <button class="topbar-toggle" id="toggleSidebar" aria-label="Abrir menú">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <div class="topbar-title">Inscripciones</div>
                <div class="topbar-breadcrumb">Gestión › Inscripciones</div>
            </div>
        </div>
        <div class="topbar-right">
            <?php if ($rol === 'CICLISTA'): ?>
            <button class="topbar-action-btn" onclick="abrirModal('modalInscripcion')">
                <i class="fas fa-plus"></i> Nueva Inscripción
            </button>
            <?php endif; ?>
            <button class="topbar-icon-btn"><i class="fas fa-bell"></i></button>
        </div>
    </header>

    <!-- ── CONTENIDO ── -->
    <main class="mtb-inner">

        <!-- PAGE HEADER -->
        <div class="page-header">
            <div class="page-header-info">
                <h1><?= $rol === 'ADMIN' ? 'Gestión de Inscripciones' : 'Mis Inscripciones' ?></h1>
                <p><?= $nombre_completo ?> · <?= $rol ?></p>
            </div>
        </div>

        <!-- ── TARJETAS ESTADÍSTICAS ── -->
        <div class="stats-grid" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:16px; margin-bottom:28px;">

            <div class="stat-card">
                <div class="stat-icon" style="background:var(--mtb-primary-bg); color:var(--mtb-primary);">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $total_ins ?></div>
                    <div class="stat-label"><?= $rol === 'ADMIN' ? 'Total Inscripciones' : 'Mis Inscripciones' ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:var(--mtb-warning-bg); color:#92400E;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $pendientes ?></div>
                    <div class="stat-label">Pagos Pendientes</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:var(--mtb-success-bg); color:#14532D;">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $confirmados ?></div>
                    <div class="stat-label">Pagos Confirmados</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:var(--mtb-danger-bg); color:#7F1D1D;">
                    <i class="fas fa-circle-xmark"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $rechazados ?></div>
                    <div class="stat-label">Rechazados</div>
                </div>
            </div>

        </div>

        <!-- ══════════════════════════════════════
             VISTA CICLISTA: Carreras disponibles
        ══════════════════════════════════════ -->
        <?php if ($rol === 'CICLISTA'): ?>

        <h2 style="font-family:var(--font-display); font-size:1.4rem; font-weight:800; color:var(--mtb-dark); margin-bottom:4px;">
            <i class="fas fa-flag-checkered" style="color:var(--mtb-primary); margin-right:8px;"></i>Carreras Disponibles
        </h2>
        <p style="color:var(--mtb-gray-600); font-size:.875rem; margin-bottom:2px;">Selecciona una carrera para inscribirte</p>

        <div class="carreras-grid">
            <?php foreach ($carreras_lista as $c):
                $inscritos  = (int)$c['totalInscritos'];
                $cupo       = (int)$c['cupo'];
                $pct        = $cupo > 0 ? min(100, round($inscritos / $cupo * 100)) : 0;
                $disponible = $c['estatusCarrera'] === 'ABIERTO' && $inscritos < $cupo;
                $cats_ids   = $c['cats_ids'] ? explode(',', $c['cats_ids']) : [];
            ?>
            <div class="card-carrera">
                <img src="assets/img/carreras/<?= htmlspecialchars($c['rutaImagen']) ?>"
                     class="card-carrera-img"
                     onerror="this.src='assets/img/carreras/default_pista.png'"
                     alt="<?= htmlspecialchars($c['nombreCarrera']) ?>">
                <div class="card-carrera-body">
                    <div class="card-carrera-title"><?= htmlspecialchars($c['nombreCarrera']) ?></div>
                    <div class="card-carrera-meta"><i class="fas fa-route"></i> <?= htmlspecialchars($c['nombrePista']) ?> — <?= htmlspecialchars($c['seccion']) ?></div>
                    <div class="card-carrera-meta"><i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($c['fechaCarrera'])) ?> · <?= substr($c['horaSalida'], 0, 5) ?> hrs</div>
                    <div style="margin-top:10px;">
                        <div style="display:flex; justify-content:space-between; font-size:.75rem; color:var(--mtb-gray-600); margin-bottom:3px;">
                            <span>Cupo</span><span><?= $inscritos ?> / <?= $cupo ?></span>
                        </div>
                        <div class="cupo-bar"><div class="cupo-fill" style="width:<?= $pct ?>%; <?= $pct >= 90 ? 'background:#ef4444' : '' ?>"></div></div>
                    </div>
                </div>
                <div class="card-carrera-footer">
                    <div>
                        <span style="font-family:var(--font-display); font-size:1.2rem; font-weight:800; color:var(--mtb-primary);">
                            $<?= number_format($c['costoInscripcion'], 2) ?>
                        </span>
                        <span style="font-size:.7rem; color:var(--mtb-gray-500);"> MXN</span>
                    </div>
                    <?php if ($c['estatusCarrera'] === 'ABIERTO' && $disponible): ?>
                        <button class="btn btn-primary btn-sm"
                                onclick='abrirModalInscripcion(<?= htmlspecialchars(json_encode($c), ENT_QUOTES) ?>)'>
                            <i class="fas fa-plus"></i> Inscribirme
                        </button>
                    <?php elseif ($c['estatusCarrera'] === 'PROXIMO'): ?>
                        <span class="badge badge-info">PRÓXIMAMENTE</span>
                    <?php elseif ($inscritos >= $cupo): ?>
                        <span class="badge badge-danger">CUPO LLENO</span>
                    <?php else: ?>
                        <span class="badge badge-dark"><?= $c['estatusCarrera'] ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($carreras_lista)): ?>
            <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--mtb-gray-500);">
                <i class="fas fa-calendar-xmark" style="font-size:2.5rem; margin-bottom:12px; display:block;"></i>
                No hay carreras disponibles actualmente.
            </div>
            <?php endif; ?>
        </div>

        <h2 style="font-family:var(--font-display); font-size:1.4rem; font-weight:800; color:var(--mtb-dark); margin:32px 0 4px;">
            <i class="fas fa-list-check" style="color:var(--mtb-primary); margin-right:8px;"></i>Mis Registros
        </h2>
        <p style="color:var(--mtb-gray-600); font-size:.875rem; margin-bottom:12px;">Historial de tus inscripciones</p>

        <?php endif; /* fin CICLISTA */ ?>


        <!-- ══════════════════════════════════════
             TABLA DE INSCRIPCIONES (ambos roles)
        ══════════════════════════════════════ -->
        <section class="tabla-seccion">
            <div class="tabla-card">
                <div class="tabla-header">
                    <h3 style="margin:0; font-size:1rem;">
                        <i class="fas fa-table" style="margin-right:8px; color:var(--mtb-primary);"></i>
                        <?= $rol === 'ADMIN' ? 'Todas las inscripciones' : 'Mis inscripciones' ?>
                    </h3>
                </div>

                <!-- Filtros -->
                <div style="padding:16px 20px 0;">
                    <div class="filtros-bar">
                        <input type="text"   class="form-control" id="buscarTexto"     placeholder="Buscar nombre o carrera..." oninput="filtrarTabla()">
                        <select class="form-control" id="filtroPago" onchange="filtrarTabla()">
                            <option value="">— Todos los pagos —</option>
                            <option value="PENDIENTE">Pendiente</option>
                            <option value="CONFIRMADO">Confirmado</option>
                            <option value="RECHAZADO">Rechazado</option>
                            <option value="EXENTO">Exento</option>
                        </select>
                        <button class="btn btn-secondary btn-sm" onclick="limpiarFiltros()">
                            <i class="fas fa-rotate-left"></i> Limpiar
                        </button>
                    </div>
                </div>

                <div class="tabla-wrap">
                    <table id="tablaInscripciones">
                        <thead>
                            <tr>
                                <th style="width:50px;">#</th>
                                <?php if ($rol === 'ADMIN'): ?>
                                <th>Ciclista</th>
                                <th>Correo</th>
                                <?php endif; ?>
                                <th>Carrera</th>
                                <th>Pista</th>
                                <th>Categoría</th>
                                <th>Fecha Inscripción</th>
                                <th>Costo</th>
                                <th>Pago</th>
                                <th style="text-align:center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyInscripciones">
                        <?php if (empty($inscripciones)): ?>
                            <tr>
                                <td colspan="10" class="table-empty">
                                    <i class="fas fa-inbox"></i>
                                    <?= $rol === 'ADMIN' ? 'No hay inscripciones registradas.' : 'Aún no tienes inscripciones.' ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inscripciones as $ins): ?>
                            <tr data-pago="<?= $ins['estadoPago'] ?>"
                                data-nombre="<?= htmlspecialchars(strtolower(($ins['nombreUser'] ?? '') . ' ' . ($ins['apellidosUser'] ?? '') . ' ' . $ins['nombreCarrera'])) ?>">

                                <td><span class="num-corredor"><?= $ins['NumeroEnLaCarrera'] ?></span></td>

                                <?php if ($rol === 'ADMIN'): ?>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div class="avatar avatar-sm">
                                            <?= strtoupper(mb_substr($ins['nombreUser'],0,1) . mb_substr($ins['apellidosUser'],0,1)) ?>
                                        </div>
                                        <span style="font-weight:600;"><?= htmlspecialchars($ins['nombreUser'] . ' ' . $ins['apellidosUser']) ?></span>
                                    </div>
                                </td>
                                <td style="font-size:.8rem; color:var(--mtb-gray-600);"><?= htmlspecialchars($ins['correoUser']) ?></td>
                                <?php endif; ?>

                                <td style="font-weight:600;"><?= htmlspecialchars($ins['nombreCarrera']) ?></td>
                                <td><?= htmlspecialchars($ins['nombrePista']) ?></td>
                                <td>
                                    <span class="badge badge-dark" style="font-size:.72rem;">
                                        <?= htmlspecialchars($ins['tipoCategoria']) ?> · <?= $ins['generoCategoria'] ?>
                                    </span>
                                </td>
                                <td style="font-size:.82rem; color:var(--mtb-gray-600);">
                                    <?= date('d M Y', strtotime($ins['fechaInscripcion'])) ?>
                                </td>
                                <td style="font-weight:700; color:var(--mtb-primary);">
                                    $<?= number_format($ins['costoInscripcion'], 2) ?>
                                </td>
                                <td>
                                    <span class="badge-pago pago-<?= $ins['estadoPago'] ?>">
                                        <?= $ins['estadoPago'] ?>
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; gap:4px; justify-content:center;">
                                        <?php if ($rol === 'ADMIN'): ?>
                                        <button class="btn btn-ghost btn-sm" title="Cambiar estado de pago"
                                                style="color:var(--mtb-primary);"
                                                onclick='abrirModalPago(<?= $ins['numeroInscripcion'] ?>, "<?= $ins['estadoPago'] ?>", "<?= htmlspecialchars($ins['nombreUser'].' '.$ins['apellidosUser']) ?>")'>
                                            <i class="fas fa-money-bill"></i>
                                        </button>
                                        <?php elseif (in_array($ins['estatusCarrera'], ['ABIERTO','PROXIMO']) && $ins['estadoPago'] !== 'CONFIRMADO'): ?>
                                        <button class="btn btn-ghost btn-sm" title="Cancelar inscripción"
                                                style="color:var(--mtb-danger);"
                                                onclick="confirmarCancelacion(<?= $ins['numeroInscripcion'] ?>, '<?= htmlspecialchars($ins['nombreCarrera']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>
</div><!-- /.mtb-content -->


<!-- ══════════════════════════════════════════════════════
     MODAL: INSCRIBIRSE A CARRERA (CICLISTA)
══════════════════════════════════════════════════════ -->
<?php if ($rol === 'CICLISTA'): ?>
<div class="modal-overlay" id="modalInscripcion">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h2><i class="fas fa-flag-checkered"></i> Inscripción a Carrera</h2>
            <button class="modal-close" onclick="cerrarModal('modalInscripcion')"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="modal-body">

            <!-- Resumen de carrera seleccionada -->
            <div id="resumenCarrera" style="background:var(--mtb-primary-bg); border-radius:var(--radius-md); padding:16px; margin-bottom:20px; display:none;">
                <div style="display:flex; gap:14px; align-items:center;">
                    <img id="ins_img" src="" style="width:70px;height:60px;object-fit:cover;border-radius:var(--radius-sm);">
                    <div>
                        <div id="ins_nombre" style="font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--mtb-dark);"></div>
                        <div id="ins_pista"  style="font-size:.82rem;color:var(--mtb-gray-600);margin-top:2px;"></div>
                        <div id="ins_fecha"  style="font-size:.82rem;color:var(--mtb-gray-600);"></div>
                    </div>
                    <div style="margin-left:auto;text-align:right;">
                        <div style="font-family:var(--font-display);font-size:1.4rem;font-weight:800;color:var(--mtb-primary);" id="ins_costo"></div>
                        <div style="font-size:.7rem;color:var(--mtb-gray-500);">MXN</div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="ins_id_carrera">

            <div class="form-group">
                <label class="form-label required">Selecciona una carrera</label>
                <select class="form-control" id="selectCarrera" onchange="onSeleccionarCarrera(this)">
                    <option value="">— Elige una carrera —</option>
                    <?php foreach ($carreras_lista as $c):
                        if ($c['estatusCarrera'] !== 'ABIERTO' || (int)$c['totalInscritos'] >= (int)$c['cupo']) continue;
                    ?>
                    <option value="<?= $c['numeroCarrera'] ?>"
                            data-json='<?= htmlspecialchars(json_encode($c), ENT_QUOTES) ?>'>
                        <?= htmlspecialchars($c['nombreCarrera']) ?> — <?= date('d M Y', strtotime($c['fechaCarrera'])) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="grupoCategoria" style="display:none;">
                <label class="form-label required">Selecciona tu categoría</label>
                <div class="cat-grid" id="gridCategorias">
                    <!-- llenado por JS -->
                </div>
                <input type="hidden" id="ins_id_categoria">
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal('modalInscripcion')">Cancelar</button>
            <button class="btn btn-primary" id="btnConfirmarInscripcion" onclick="enviarInscripcion()" disabled>
                <i class="fas fa-check"></i> Confirmar Inscripción
            </button>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- ══════════════════════════════════════════════════════
     MODAL: ACTUALIZAR PAGO (ADMIN)
══════════════════════════════════════════════════════ -->
<?php if ($rol === 'ADMIN'): ?>
<div class="modal-overlay" id="modalPago">
    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-money-bill-wave"></i> Estado de Pago</h2>
            <button class="modal-close" onclick="cerrarModal('modalPago')"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="pago_id_inscripcion">
            <p style="margin-bottom:16px;">Ciclista: <strong id="pago_nombre_ciclista"></strong></p>
            <div class="form-group">
                <label class="form-label required">Nuevo estado de pago</label>
                <select class="form-control" id="selectEstadoPago">
                    <option value="PENDIENTE">Pendiente</option>
                    <option value="CONFIRMADO">Confirmado</option>
                    <option value="RECHAZADO">Rechazado</option>
                    <option value="EXENTO">Exento</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal('modalPago')">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarPago()">
                <i class="fas fa-save"></i> Guardar
            </button>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- ── TOAST ── -->
<div class="toast-container" id="toastContainer"></div>

<!-- ══════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ─── Datos de PHP ───────────────────────────────────────────
const CATEGORIAS = <?= $categorias_json ?>;
const CARRERAS   = <?= $carreras_json ?>;

// ─── Utilidades modales ─────────────────────────────────────
function abrirModal(id)  { document.getElementById(id).classList.add('active'); }
function cerrarModal(id) { document.getElementById(id).classList.remove('active'); }

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) cerrarModal(o.id); });
});

// ─── Sidebar ────────────────────────────────────────────────
document.getElementById('toggleSidebar').addEventListener('click', function () {
    document.getElementById('mtbSidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
});
document.getElementById('sidebarOverlay').addEventListener('click', function () {
    document.getElementById('mtbSidebar').classList.remove('open');
    this.classList.remove('active');
});

// ─── Toast ──────────────────────────────────────────────────
function showToast(msg, tipo = 'primary') {
    const iconos  = { success:'fa-circle-check', danger:'fa-circle-xmark', warning:'fa-triangle-exclamation', info:'fa-circle-info', primary:'fa-bell' };
    const colores = { success:'var(--mtb-success)', danger:'var(--mtb-danger)', warning:'var(--mtb-warning)', info:'var(--mtb-info)', primary:'var(--mtb-primary)' };
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast';
    t.style.borderLeftColor = colores[tipo] || colores.primary;
    t.innerHTML = `<i class="fas ${iconos[tipo]||iconos.primary}" style="color:${colores[tipo]||colores.primary};"></i><span>${msg}</span>`;
    c.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transform='translateX(30px)'; t.style.transition='all .3s'; setTimeout(()=>t.remove(),300); }, 3500);
}

// ─── Filtrar tabla ──────────────────────────────────────────
function filtrarTabla() {
    const texto = document.getElementById('buscarTexto').value.toLowerCase().trim();
    const pago  = document.getElementById('filtroPago').value;
    document.querySelectorAll('#tbodyInscripciones tr[data-pago]').forEach(tr => {
        const matchPago   = !pago  || tr.dataset.pago   === pago;
        const matchTexto  = !texto || tr.dataset.nombre.includes(texto);
        tr.style.display = matchPago && matchTexto ? '' : 'none';
    });
}
function limpiarFiltros() {
    document.getElementById('buscarTexto').value = '';
    document.getElementById('filtroPago').value  = '';
    filtrarTabla();
}

// ════════════════════════════════════════════════════════════
//  MÓDULO CICLISTA: Inscripción
// ════════════════════════════════════════════════════════════
function abrirModalInscripcion(carrera) {
    // Precarga la carrera si llega por parámetro (btn "Inscribirme")
    if (carrera) {
        const sel = document.getElementById('selectCarrera');
        sel.value = carrera.numeroCarrera;
        onSeleccionarCarreraData(carrera);
    }
    abrirModal('modalInscripcion');
}

function onSeleccionarCarrera(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) { resetModalInscripcion(); return; }
    const carrera = JSON.parse(opt.dataset.json);
    onSeleccionarCarreraData(carrera);
}

function onSeleccionarCarreraData(carrera) {
    // Mostrar resumen
    document.getElementById('ins_id_carrera').value = carrera.numeroCarrera;
    document.getElementById('ins_nombre').textContent = carrera.nombreCarrera;
    document.getElementById('ins_pista').textContent  = '📍 ' + carrera.nombrePista + ' — ' + carrera.seccion;
    document.getElementById('ins_fecha').textContent  = '📅 ' + new Date(carrera.fechaCarrera).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'}) + ' · ' + (carrera.horaSalida||'').substring(0,5) + ' hrs';
    document.getElementById('ins_costo').textContent  = '$' + parseFloat(carrera.costoInscripcion).toFixed(2);
    document.getElementById('ins_img').src = 'assets/img/carreras/' + carrera.rutaImagen;
    document.getElementById('ins_img').onerror = function(){ this.src='assets/img/carreras/default_pista.png'; };
    document.getElementById('resumenCarrera').style.display = 'block';

    // Filtrar categorías habilitadas en esa carrera
    const catsHabilitadas = carrera.cats_ids ? String(carrera.cats_ids).split(',').map(Number) : [];
    const filtradas = CATEGORIAS.filter(cat => catsHabilitadas.includes(cat.numeroCategoria));

    const grid = document.getElementById('gridCategorias');
    grid.innerHTML = '';

    if (filtradas.length === 0) {
        grid.innerHTML = '<p style="color:var(--mtb-gray-600);font-size:.85rem;">No hay categorías asignadas a esta carrera.</p>';
    } else {
        filtradas.forEach(cat => {
            const div = document.createElement('div');
            div.className = 'cat-card';
            div.dataset.id = cat.numeroCategoria;
            div.innerHTML = `
                <div class="cat-card-name">${cat.tipoCategoria}</div>
                <div class="cat-card-meta">${cat.generoCategoria} · ${cat.edadMinima}–${cat.edadMaxima} años</div>`;
            div.addEventListener('click', () => seleccionarCategoria(div, cat.numeroCategoria));
            grid.appendChild(div);
        });
    }

    document.getElementById('ins_id_categoria').value = '';
    document.getElementById('grupoCategoria').style.display = 'block';
    document.getElementById('btnConfirmarInscripcion').disabled = true;
}

function seleccionarCategoria(card, id) {
    document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('ins_id_categoria').value = id;
    document.getElementById('btnConfirmarInscripcion').disabled = false;
}

function resetModalInscripcion() {
    document.getElementById('ins_id_carrera').value = '';
    document.getElementById('ins_id_categoria').value = '';
    document.getElementById('resumenCarrera').style.display = 'none';
    document.getElementById('grupoCategoria').style.display = 'none';
    document.getElementById('btnConfirmarInscripcion').disabled = true;
}

function enviarInscripcion() {
    const idCarrera   = document.getElementById('ins_id_carrera').value;
    const idCategoria = document.getElementById('ins_id_categoria').value;

    if (!idCarrera || !idCategoria) {
        showToast('Selecciona una carrera y una categoría.', 'warning');
        return;
    }

    const btn = document.getElementById('btnConfirmarInscripcion');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

    const fd = new FormData();
    fd.append('accion',       'inscribir');
    fd.append('id_carrera',   idCarrera);
    fd.append('id_categoria', idCategoria);

    fetch('actions/procesar_inscripcion.php', { method:'POST', body:fd })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                cerrarModal('modalInscripcion');
                Swal.fire({
                    title: '¡Inscripción exitosa!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#E8630A'
                }).then(() => location.reload());
            } else {
                showToast(data.message, 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Confirmar Inscripción';
            }
        })
        .catch(() => {
            showToast('Error de conexión. Intenta de nuevo.', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Confirmar Inscripción';
        });
}

// ─── Cancelar inscripción ────────────────────────────────────
function confirmarCancelacion(idIns, nombreCarrera) {
    Swal.fire({
        title: '¿Cancelar inscripción?',
        html: `Estás por cancelar tu inscripción en <strong>${nombreCarrera}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor:  '#6B7280',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText:  'No, conservar'
    }).then(result => {
        if (!result.isConfirmed) return;

        const fd = new FormData();
        fd.append('accion',          'cancelar');
        fd.append('id_inscripcion',  idIns);

        fetch('actions/procesar_inscripcion.php', { method:'POST', body:fd })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ title:'Cancelada', text:data.message, icon:'success', confirmButtonColor:'#E8630A' })
                        .then(() => location.reload());
                } else {
                    showToast(data.message, 'danger');
                }
            })
            .catch(() => showToast('Error de conexión.', 'danger'));
    });
}

// ════════════════════════════════════════════════════════════
//  MÓDULO ADMIN: Cambiar estado de pago
// ════════════════════════════════════════════════════════════
function abrirModalPago(idIns, estadoActual, nombreCiclista) {
    document.getElementById('pago_id_inscripcion').value = idIns;
    document.getElementById('pago_nombre_ciclista').textContent = nombreCiclista;
    document.getElementById('selectEstadoPago').value = estadoActual;
    abrirModal('modalPago');
}

function guardarPago() {
    const idIns  = document.getElementById('pago_id_inscripcion').value;
    const estado = document.getElementById('selectEstadoPago').value;

    const fd = new FormData();
    fd.append('accion',          'actualizar_pago');
    fd.append('id_inscripcion',  idIns);
    fd.append('estado_pago',     estado);

    fetch('actions/procesar_inscripcion.php', { method:'POST', body:fd })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                cerrarModal('modalPago');
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast(data.message, 'danger');
            }
        })
        .catch(() => showToast('Error de conexión.', 'danger'));
}

// ─── Verificar estatus cada 5 seg ───────────────────────────
setInterval(() => {
    fetch('verificar_estatus.php').then(r=>r.json()).then(d => {
        if (d.activo === false) window.location.href = 'index.php?error=Tu cuenta ha sido desactivada.';
    }).catch(()=>{});
}, 5000);

// ─── Mensajes por URL ────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const msg    = params.get('msg');
    if (msg === 'inscrito') {
        Swal.fire({ title:'¡Inscripción exitosa!', icon:'success', confirmButtonColor:'#E8630A' })
            .then(() => window.history.replaceState({}, document.title, window.location.pathname));
    }
});

function confirmarCierreSesion(event) {
    event.preventDefault();
    Swal.fire({
        title: '¿Seguro que quieres cerrar sesión?',
        text: 'Tendrás que volver a ingresar tus credenciales.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E8630A',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Cancelar'
    }).then(r => { if (r.isConfirmed) window.location.href = 'actions/cerrarSesion.php'; });
}
</script>

<?php include_once 'includes/footer_scripts.php'; ?>
</body>
</html>
