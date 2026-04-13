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

// ==========================================
// CONSULTAS BASE (Catálogos)
// ==========================================
$pistas = $conn->query("SELECT numeroPista, nombrePista FROM pistas WHERE estatus = 'ACTIVO'")->fetchAll(PDO::FETCH_ASSOC);
$patrocinadores = $conn->query("SELECT numeroPatrocinador, nombrePatrocinador, logo_patrocinador FROM patrocinador WHERE estatus = 'ACTIVO'")->fetchAll(PDO::FETCH_ASSOC);
$categorias = $conn->query("SELECT * FROM categorias ORDER BY edadMinima ASC")->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// CONSULTA DE CARRERAS (Con patrocinadores y categorías)
// ==========================================
try {
    $sql_base = "SELECT c.*, p.nombrePista, 
                 (SELECT GROUP_CONCAT(pat.logo_patrocinador SEPARATOR ',') 
                  FROM carreras_patrocinadores cp 
                  INNER JOIN patrocinador pat ON cp.numeroPatrocinador = pat.numeroPatrocinador 
                  WHERE cp.numeroCarrera = c.numeroCarrera) as logos_patrocinadores,
                 (SELECT GROUP_CONCAT(numeroPatrocinador) 
                  FROM carreras_patrocinadores 
                  WHERE numeroCarrera = c.numeroCarrera) as patrocinadores_ids,
                 (SELECT GROUP_CONCAT(numeroCategoria) 
                  FROM carreras_categorias 
                  WHERE numeroCarrera = c.numeroCarrera) as categorias_ids
                 FROM carreras c 
                 INNER JOIN pistas p ON c.numeroPista = p.numeroPista";

    if ($rol === 'ADMIN') {
        $stmt_carreras = $conn->query("$sql_base ORDER BY c.fechaCarrera DESC");
    } else {
        $stmt_carreras = $conn->query("$sql_base WHERE c.estatus = 'ACTIVO' AND c.estatusCarrera IN ('PROXIMO', 'ABIERTO') ORDER BY c.fechaCarrera ASC");
    }
    
    $carreras = $stmt_carreras->fetchAll(PDO::FETCH_ASSOC);
    $categorias_json = json_encode($categorias, JSON_UNESCAPED_UNICODE);

    $total_carreras = count($carreras);
    $abiertas = 0;
    foreach($carreras as $c) if($c['estatusCarrera'] === 'ABIERTO') $abiertas++;

} catch (PDOException $e) {
    $carreras = []; $total_carreras = 0; $abiertas = 0;
}
?>

<?php include_once 'includes/header_sidebar.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Carreras</title>
    <link rel="stylesheet" href="assets/css/mtb-dashboard.css">
    <style>
        .grid-cliente { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; margin-top: 20px; }
        .card-carrera { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee; transition: 0.3s; }
        .card-carrera:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(232,99,10,0.15); border-color: var(--mtb-primary); }
        .carrera-img { width: 100%; height: 200px; object-fit: cover; border-bottom: 4px solid var(--mtb-primary); }
        .carrera-body { padding: 20px; }
        .carrera-fecha { color: var(--mtb-primary); font-weight: bold; font-size: 0.9rem; margin-bottom: 5px; }
        .carrera-title { font-family: var(--font-display); font-size: 1.5rem; color: var(--mtb-dark); margin: 0 0 10px 0; line-height: 1.2; }
        .carrera-info { display: flex; gap: 15px; font-size: 0.85rem; color: #666; margin-bottom: 15px; }
        .carrera-info i { color: var(--mtb-primary); }
        .carrera-sponsors img { height: 25px; object-fit: contain; margin-right: 5px; border-radius: 4px; background: #f9f9f9; padding: 2px;}
        .chk-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; max-height: 150px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        .chk-item { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="mtb-content">
        <header class="mtb-topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="toggleSidebar"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="topbar-title">Temporada de Carreras</div>
                    <div class="topbar-breadcrumb">Carreras › Resumen</div>
                </div>
            </div>
            <div class="topbar-right">
                <?php if ($rol === 'ADMIN'): ?>
                <button class="topbar-action-btn" style="background: var(--mtb-dark);" onclick="abrirModal('modalCategorias')">
                    <i class="fas fa-tags"></i> Categorías
                </button>
                <button class="topbar-action-btn" onclick="abrirModal('modalCarrera')">
                    <i class="fas fa-flag-checkered"></i> Nueva Carrera
                </button>
                <?php endif; ?>
                <button class="topbar-icon-btn"><i class="fas fa-bell"></i></button>
            </div>
        </header>

        <main class="mtb-inner">
            <?php if ($rol === 'ADMIN'): ?>
            <div class="mtb-stats-grid">
                <div class="stat-card" style="border-left: 4px solid var(--mtb-primary);">
                    <div class="stat-icon" style="background: rgba(232, 99, 10, 0.1); color: var(--mtb-primary);"><i class="fas fa-flag-checkered"></i></div>
                    <div class="stat-details"><h3><?= $total_carreras ?></h3><p>Eventos Totales</p></div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #28a745;">
                    <div class="stat-icon" style="background: rgba(40, 167, 69, 0.1); color: #28a745;"><i class="fas fa-door-open"></i></div>
                    <div class="stat-details"><h3><?= $abiertas ?></h3><p>Inscripciones Abiertas</p></div>
                </div>
            </div>

            <section class="tabla-seccion" style="margin-top: 20px;">
                <div class="tabla-card">
                    <div class="tabla-header">
                        <h3 style="margin:0; font-size: 1.1rem;"><i class="fas fa-calendar-alt"></i> Gestión de Carreras</h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="mtb-table">
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Detalles de la Carrera</th>
                                    <th>Sede / Pista</th>
                                    <th style="text-align: center;">Inscripción / Cupo</th>
                                    <th style="text-align: center;">Estado Evento</th>
                                    <th style="text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carreras as $c): ?>
                                <tr style="<?= $c['estatus'] == 'INACTIVO' ? 'opacity: 0.6; background: #fdfdfd;' : '' ?>">
                                    <td style="color: #555;">
                                        <div style="font-weight: 700;"><i class="far fa-calendar-alt text-primary"></i> <?= date('d/m/Y', strtotime($c['fechaCarrera'])) ?></div>
                                        <div style="font-size: 0.8rem; margin-top: 3px;"><i class="far fa-clock text-primary"></i> <?= date('h:i A', strtotime($c['horaSalida'])) ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 800; color: var(--mtb-dark); font-size: 1.05rem;"><?= htmlspecialchars($c['nombreCarrera']) ?></div>
                                        <div style="font-size: 0.8rem; color: #666; margin-top: 4px; display: flex; gap: 12px;">
                                            <span><i class="fas fa-route text-primary"></i> <?= $c['kilometros'] ?> Km</span>
                                            <span><i class="fas fa-sync-alt text-primary"></i> <?= $c['vueltas'] ?> Vueltas</span>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-dark"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($c['nombrePista']) ?></span></td>
                                    <td style="text-align: center;">
                                        <div style="font-weight: 800; font-size: 1.1rem; color: <?= $c['costoInscripcion'] > 0 ? 'var(--mtb-dark)' : 'var(--mtb-success)' ?>;">
                                            <?= $c['costoInscripcion'] > 0 ? '$'.number_format($c['costoInscripcion'], 2) : 'GRATIS' ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #666; margin-top: 2px;"><i class="fas fa-users"></i> Max. <?= $c['cupo'] ?></div>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php 
                                            $colores = ['PROXIMO'=>'primary', 'ABIERTO'=>'success', 'CERRADO'=>'warning', 'CONCLUIDO'=>'dark'];
                                            $color = $colores[$c['estatusCarrera']] ?? 'primary';
                                        ?>
                                        <span class="badge badge-<?= $color ?>"><?= $c['estatusCarrera'] ?></span>
                                    </td>
                                    <td style="text-align: center; display: flex; gap: 8px; justify-content: center; align-items: center; height: 100%;">
                                        <a href="#" class="btn-accion btn-actualizar" style="border: 1px solid #ff6b00;" 
                                           onclick="abrirModalEditarCarrera(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if($c['estatus'] == 'ACTIVO'): ?>
                                            <a href="actions/estatus_carrera.php?id=<?= $c['numeroCarrera'] ?>&estado=INACTIVO" class="btn-accion btn-desactivar" title="Ocultar del sistema"><i class="fas fa-eye-slash"></i></a>
                                        <?php else: ?>
                                            <a href="actions/estatus_carrera.php?id=<?= $c['numeroCarrera'] ?>&estado=ACTIVO" class="btn-accion btn-reactivar" title="Mostrar en sistema"><i class="fas fa-eye"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($rol === 'CICLISTA'): ?>
            <h2 style="font-family: var(--font-display); color: var(--mtb-dark);">Próximos Desafíos</h2>
            <div class="grid-cliente">
                <?php foreach ($carreras as $c): ?>
                <div class="card-carrera">
                    <img src="assets/img/carreras/<?= $c['rutaImagen'] ?>" class="carrera-img" onerror="this.src='assets/img/carreras/default_carrera.png'">
                    <div class="carrera-body">
                        <div class="carrera-fecha"><i class="far fa-calendar-alt"></i> <?= date('d M, Y', strtotime($c['fechaCarrera'])) ?> - <?= date('h:i A', strtotime($c['horaSalida'])) ?></div>
                        <h3 class="carrera-title"><?= htmlspecialchars($c['nombreCarrera']) ?></h3>
                        
                        <div class="carrera-info">
                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($c['nombrePista']) ?></span>
                            <span><i class="fas fa-route"></i> <?= $c['kilometros'] ?> km</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; padding-top: 15px; margin-top: 10px;">
                            <div>
                                <span style="display:block; font-size:0.7rem; color:#888;">INSCRIPCIÓN</span>
                                <strong style="color: var(--mtb-dark);"><?= $c['costoInscripcion'] > 0 ? '$'.number_format($c['costoInscripcion'],2) : 'GRATUITA' ?></strong>
                            </div>
                            
                            <?php if($c['estatusCarrera'] === 'ABIERTO'): ?>
                                <button class="btn btn-primary btn-sm" onclick="abrirModalDetallesCarrera(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)">Ver Detalles</button>
                            <?php else: ?>
                                <span class="badge badge-primary">PRÓXIMAMENTE</span>
                            <?php endif; ?>

                        </div>

                        <?php if(!empty($c['logos_patrocinadores'])): ?>
                        <div class="carrera-sponsors" style="margin-top: 15px; padding-top: 10px; border-top: 1px dashed #eee;">
                            <span style="font-size: 0.7rem; color: #999; display: block; margin-bottom: 5px;">PATROCINADO POR:</span>
                            <?php 
                                $logos = explode(',', $c['logos_patrocinadores']);
                                foreach($logos as $logo) { echo "<img src='assets/img/patrocinadores/".trim($logo)."'>"; }
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <?php if ($rol === 'ADMIN'): ?>
    <div class="modal-overlay" id="modalCarrera">
        <div class="modal modal-lg"> 
            <div class="modal-header">
                <h2><i class="fas fa-flag-checkered"></i> Configurar Carrera</h2>
                <button class="modal-close" onclick="cerrarModal('modalCarrera')"><i class="fas fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="actions/procesar_carrera.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Nombre de la Carrera</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Sede / Pista</label>
                            <select name="pista" class="form-control" required>
                                <option value="">Selecciona una pista...</option>
                                <?php foreach($pistas as $p): ?>
                                    <option value="<?= $p['numeroPista'] ?>"><?= $p['nombrePista'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label required">Fecha</label>
                            <input type="date" class="form-control" name="fecha" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Hora de Salida</label>
                            <input type="time" class="form-control" name="hora" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Estado del Evento</label>
                            <select name="estado_evento" class="form-control" required>
                                <option value="PROXIMO">Próximo (Aún no inscripciones)</option>
                                <option value="ABIERTO">Abierto (Recibiendo inscripciones)</option>
                                <option value="CERRADO">Cerrado (Lleno/Concluido inscripciones)</option>
                                <option value="CONCLUIDO">Concluido (Carrera finalizada)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label required">Kilómetros (Km)</label>
                            <input type="number" step="0.1" class="form-control" name="km" value="0.0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Vueltas</label>
                            <input type="number" class="form-control" name="vueltas" value="1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Cupo Máximo</label>
                            <input type="number" class="form-control" name="cupo" value="100" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Costo Inscripción ($)</label>
                            <input type="number" step="0.01" class="form-control" name="costo" value="0.00" required>
                            <small class="form-hint">Pon 0 para evento gratuito.</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Poster / Imagen promocional</label>
                            <input type="file" class="form-control" name="imagen" accept=".png, .jpg, .jpeg">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Patrocinadores</label>
                            <div class="chk-grid">
                                <?php foreach($patrocinadores as $pat): ?>
                                <label class="chk-item">
                                    <input type="checkbox" name="patrocinadores[]" value="<?= $pat['numeroPatrocinador'] ?>"> 
                                    <img src="assets/img/patrocinadores/<?= $pat['logo_patrocinador'] ?>" style="height:15px; width:15px; object-fit:contain;">
                                    <?= htmlspecialchars($pat['nombrePatrocinador']) ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Categorías Participantes</label>
                            <div class="chk-grid">
                                <?php foreach($categorias as $cat): ?>
                                <label class="chk-item">
                                    <input type="checkbox" name="categorias[]" value="<?= $cat['numeroCategoria'] ?>"> 
                                    <?= htmlspecialchars($cat['tipoCategoria']) ?> (<?= $cat['generoCategoria'] ?>)
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Descripción o Reglas</label>
                        <textarea class="form-control" name="descripcion" rows="4"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalCarrera')">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Carrera</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalEditarCarrera">
        <div class="modal modal-lg">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Editar Carrera</h2>
                <button type="button" class="modal-close" onclick="cerrarModal('modalEditarCarrera')"><i class="fas fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="actions/actualizar_carrera.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_carrera" id="edit_id_carrera">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Nombre de la Carrera</label>
                            <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Sede / Pista</label>
                            <select name="pista" id="edit_pista" class="form-control" required>
                                <option value="">Selecciona una pista...</option>
                                <?php foreach($pistas as $p): ?>
                                    <option value="<?= $p['numeroPista'] ?>"><?= $p['nombrePista'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label required">Fecha</label>
                            <input type="date" class="form-control" name="fecha" id="edit_fecha" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Hora de Salida</label>
                            <input type="time" class="form-control" name="hora" id="edit_hora" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Estado del Evento</label>
                            <select name="estado_evento" id="edit_estado_evento" class="form-control" required>
                                <option value="PROXIMO">Próximo (Aún no inscripciones)</option>
                                <option value="ABIERTO">Abierto (Recibiendo inscripciones)</option>
                                <option value="CERRADO">Cerrado (Lleno/Concluido)</option>
                                <option value="CONCLUIDO">Concluido (Carrera finalizada)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label required">Kilómetros (Km)</label>
                            <input type="number" step="0.1" class="form-control" name="km" id="edit_km" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Vueltas</label>
                            <input type="number" class="form-control" name="vueltas" id="edit_vueltas" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Cupo Máximo</label>
                            <input type="number" class="form-control" name="cupo" id="edit_cupo" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Costo Inscripción ($)</label>
                            <input type="number" step="0.01" class="form-control" name="costo" id="edit_costo" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Actualizar Poster (Opcional)</label>
                            <input type="file" class="form-control" name="imagen" accept=".png, .jpg, .jpeg">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Patrocinadores de esta Carrera</label>
                            <div class="chk-grid">
                                <?php foreach($patrocinadores as $pat): ?>
                                <label class="chk-item">
                                    <input type="checkbox" name="patrocinadores[]" value="<?= $pat['numeroPatrocinador'] ?>" class="edit-sponsor-checkbox"> 
                                    <img src="assets/img/patrocinadores/<?= $pat['logo_patrocinador'] ?>" style="height:18px; width:18px; object-fit:contain; border-radius:3px;">
                                    <?= htmlspecialchars($pat['nombrePatrocinador']) ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Categorías Participantes</label>
                            <div class="chk-grid">
                                <?php foreach($categorias as $cat): ?>
                                <label class="chk-item">
                                    <input type="checkbox" name="categorias[]" value="<?= $cat['numeroCategoria'] ?>" class="edit-category-checkbox"> 
                                    <?= htmlspecialchars($cat['tipoCategoria']) ?> (<?= $cat['generoCategoria'] ?>)
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descripción o Reglas</label>
                        <textarea class="form-control" name="descripcion" id="edit_descripcion" rows="4"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarCarrera')">Cancelar</button>
                        <button type="submit" class="btn btn-primary" style="background: #ff6b00; border: none;"><i class="fas fa-save"></i> Actualizar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalCategorias">
        <div class="modal modal-lg">
            <div class="modal-header">
                <h2><i class="fas fa-tags"></i> Gestionar Categorías</h2>
                <button class="modal-close" onclick="cerrarModal('modalCategorias')"><i class="fas fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="actions/procesar_categoria.php" method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #dee2e6;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre (Ej. Master)" required>
                        <select name="genero" class="form-control" required>
                            <option value="VARONIL">Varonil</option>
                            <option value="FEMENIL">Femenil</option>
                            <option value="MIXTO">Mixto</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: center;">
                        <input type="number" name="min" class="form-control" placeholder="Edad Mínima" required>
                        <input type="number" name="max" class="form-control" placeholder="Edad Máxima" required>
                        <button type="submit" class="btn btn-primary" style="height: 40px; width: 40px; padding: 0; display: flex; justify-content: center; align-items: center;" title="Agregar">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </form>

                <div style="overflow-x: auto; border: 1px solid var(--mtb-gray-200); border-radius: 8px;">
                    <table class="mtb-table" style="font-size: 0.85rem; min-width: 100%;">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Género</th>
                                <th>Rango de Edades</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($categorias as $cat): ?>
                                <tr>
                                    <td style="font-weight: 700; color: var(--mtb-dark);"><?= htmlspecialchars($cat['tipoCategoria']) ?></td>
                                    <td><span class="badge badge-dark"><?= $cat['generoCategoria'] ?></span></td>
                                    <td><?= $cat['edadMinima'] ?> a <?= $cat['edadMaxima'] ?> años</td>
                                    <td style="text-align: center;">
                                        <a href="actions/eliminar_categoria.php?id=<?= $cat['numeroCategoria'] ?>" 
                                           class="btn-accion btn-delete" 
                                           title="Eliminar Categoría"
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($rol === 'CICLISTA'): ?>
    <div class="modal-overlay" id="modalDetallesCarrera">
        <div class="modal modal-lg" style="overflow: hidden; padding: 0;">
            <div style="display: flex; flex-wrap: wrap;">
                
                <div style="flex: 1 1 350px; background: #f8f9fa; border-right: 1px solid var(--mtb-gray-200); display: flex; flex-direction: column;">
                    <img id="detalle_imagen" src="" alt="Póster" style="width: 100%; height: 300px; object-fit: cover; border-bottom: 4px solid var(--mtb-primary);">
                    
                    <div style="padding: 20px;">
                        <h4 style="margin: 0 0 10px 0; color: var(--mtb-gray-600); font-size: 0.85rem; text-transform: uppercase;">Patrocinadores Oficiales</h4>
                        <div id="detalle_patrocinadores" style="display: flex; flex-wrap: wrap; gap: 10px;">
                        </div>
                    </div>
                </div>

                <div style="flex: 1 1 400px; padding: 30px; display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                        <div>
                            <span id="detalle_fecha" style="color: var(--mtb-primary); font-weight: bold; font-size: 0.9rem;"></span>
                            <h2 id="detalle_nombre" style="font-family: var(--font-display); font-size: 2rem; color: var(--mtb-dark); margin: 5px 0 0 0; line-height: 1.1;"></h2>
                        </div>
                        <button class="modal-close" onclick="cerrarModal('modalDetallesCarrera')" style="margin: -10px -10px 0 0;"><i class="fas fa-xmark"></i></button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; padding: 15px; background: var(--mtb-gray-100); border-radius: 12px;">
                        <div>
                            <span style="display: block; font-size: 0.75rem; color: var(--mtb-gray-600); text-transform: uppercase;">Ubicación</span>
                            <strong id="detalle_pista" style="color: var(--mtb-dark);"></strong>
                        </div>
                        <div>
                            <span style="display: block; font-size: 0.75rem; color: var(--mtb-gray-600); text-transform: uppercase;">Distancia</span>
                            <strong style="color: var(--mtb-dark);"><span id="detalle_km"></span> km (<span id="detalle_vueltas"></span> vueltas)</strong>
                        </div>
                        <div>
                            <span style="display: block; font-size: 0.75rem; color: var(--mtb-gray-600); text-transform: uppercase;">Inscripción</span>
                            <strong id="detalle_costo" style="color: var(--mtb-success); font-size: 1.1rem;"></strong>
                        </div>
                        <div>
                            <span style="display: block; font-size: 0.75rem; color: var(--mtb-gray-600); text-transform: uppercase;">Cupo Restante</span>
                            <strong style="color: var(--mtb-dark);"><span id="detalle_cupo"></span> lugares</strong>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <h4 style="margin: 0 0 8px 0; color: var(--mtb-dark); font-size: 1rem;">Sobre el evento</h4>
                        <p id="detalle_descripcion" style="font-size: 0.9rem; color: var(--mtb-gray-600); line-height: 1.5; white-space: pre-wrap;"></p>
                    </div>

                    <div style="margin-bottom: auto;">
                        <h4 style="margin: 0 0 8px 0; color: var(--mtb-dark); font-size: 1rem;">Categorías Disponibles</h4>
                        <div id="detalle_categorias" style="display: flex; flex-wrap: wrap; gap: 8px;">
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--mtb-gray-200); text-align: right;">
                        <button type="button" class="btn btn-primary btn-lg" onclick="alert('Módulo de Inscripciones en construcción. Aquí el ciclista elegirá su categoría y procederá.')" style="width: 100%; justify-content: center;">
                            <i class="fas fa-ticket-alt"></i> ¡Quiero Inscribirme!
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function abrirModal(id) { document.getElementById(id).classList.add('active'); }
        function cerrarModal(id) { document.getElementById(id).classList.remove('active'); }
        
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            document.getElementById('mtbSidebar').classList.toggle('open');
        });

        // MANEJO DE ALERTAS POR URL (ESTILO CLÁSICO PISTAS)
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const msg = urlParams.get('msg');

            if (msg === 'carrera_creada') {
                Swal.fire({
                    title: '¡Carrera Creada!',
                    text: 'El evento se registró correctamente.',
                    icon: 'success',
                    confirmButtonColor: '#E8630A'
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }

            if (msg === 'actualizado_ok') {
                Swal.fire({
                    title: '¡Actualizado!',
                    text: 'Los cambios se han guardado correctamente.',
                    icon: 'success',
                    confirmButtonColor: '#E8630A'
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }
        });

        <?php if ($rol === 'ADMIN'): ?>
        function abrirModalEditarCarrera(carrera) {
            document.getElementById('edit_id_carrera').value = carrera.numeroCarrera;
            document.getElementById('edit_nombre').value = carrera.nombreCarrera;
            document.getElementById('edit_pista').value = carrera.numeroPista;
            document.getElementById('edit_fecha').value = carrera.fechaCarrera.split(' ')[0]; 
            document.getElementById('edit_hora').value = carrera.horaSalida;
            document.getElementById('edit_estado_evento').value = carrera.estatusCarrera;
            document.getElementById('edit_km').value = carrera.kilometros;
            document.getElementById('edit_vueltas').value = carrera.vueltas;
            document.getElementById('edit_cupo').value = carrera.cupo;
            document.getElementById('edit_costo').value = carrera.costoInscripcion;
            document.getElementById('edit_descripcion').value = carrera.descripcion;

            // Llenar Patrocinadores
            const checkboxesPatros = document.querySelectorAll('.edit-sponsor-checkbox');
            checkboxesPatros.forEach(chk => chk.checked = false);
            if(carrera.patrocinadores_ids) {
                const idsP = carrera.patrocinadores_ids.split(',');
                idsP.forEach(id => {
                    const chk = document.querySelector(`.edit-sponsor-checkbox[value="${id}"]`);
                    if(chk) chk.checked = true;
                });
            }

            // Llenar Categorías
            const checkCats = document.querySelectorAll('.edit-category-checkbox');
            checkCats.forEach(chk => chk.checked = false);
            if(carrera.categorias_ids) {
                const idsCat = carrera.categorias_ids.split(',');
                idsCat.forEach(id => {
                    const chk = document.querySelector(`.edit-category-checkbox[value="${id}"]`);
                    if(chk) chk.checked = true;
                });
            }

            abrirModal('modalEditarCarrera');
        }
        <?php endif; ?>

        <?php if ($rol === 'CICLISTA'): ?>
        const catalogoCategorias = <?= $categorias_json ?? '[]' ?>;

        function abrirModalDetallesCarrera(carrera) {
            document.getElementById('detalle_nombre').textContent = carrera.nombreCarrera;
            document.getElementById('detalle_pista').textContent = carrera.nombrePista;
            document.getElementById('detalle_km').textContent = carrera.kilometros;
            document.getElementById('detalle_vueltas').textContent = carrera.vueltas;
            document.getElementById('detalle_cupo').textContent = carrera.cupo;
            document.getElementById('detalle_descripcion').textContent = carrera.descripcion || 'Sin descripción adicional proporcionada.';
            
            const [year, month, day] = carrera.fechaCarrera.split(' ')[0].split('-');
            document.getElementById('detalle_fecha').textContent = `${day}/${month}/${year} a las ${carrera.horaSalida.substring(0,5)}`;

            const costo = parseFloat(carrera.costoInscripcion);
            document.getElementById('detalle_costo').textContent = costo > 0 ? `$${costo.toFixed(2)}` : 'GRATUITA';
            document.getElementById('detalle_imagen').src = 'assets/img/carreras/' + (carrera.rutaImagen || 'default_carrera.png');

            // Patrocinadores
            const contenedorPatro = document.getElementById('detalle_patrocinadores');
            contenedorPatro.innerHTML = '';
            if (carrera.logos_patrocinadores) {
                const logos = carrera.logos_patrocinadores.split(',');
                logos.forEach(logo => {
                    contenedorPatro.innerHTML += `<img src="assets/img/patrocinadores/${logo.trim()}" style="height: 35px; width: auto; object-fit: contain; background: #fff; padding: 4px; border-radius: 4px; border: 1px solid #eee;">`;
                });
            } else {
                contenedorPatro.innerHTML = '<span style="font-size: 0.8rem; color: #999;">Sin patrocinadores asignados.</span>';
            }

            // Categorías Filtradas
            const contenedorCat = document.getElementById('detalle_categorias');
            contenedorCat.innerHTML = '';
            if (carrera.categorias_ids && catalogoCategorias.length > 0) {
                const idsAsignadas = carrera.categorias_ids.split(',');
                catalogoCategorias.forEach(cat => {
                    if(idsAsignadas.includes(cat.numeroCategoria.toString())) {
                        contenedorCat.innerHTML += `<span class="badge badge-dark" style="background: #e9ecef; color: #495057; border: 1px solid #ced4da; padding: 6px 12px; font-weight: normal;">${cat.tipoCategoria} (${cat.edadMinima}-${cat.edadMaxima} años)</span>`;
                    }
                });
            } else {
                contenedorCat.innerHTML = '<span style="font-size: 0.8rem; color: #999;">Aún no se han asignado categorías a este evento.</span>';
            }

            abrirModal('modalDetallesCarrera');
        }
        <?php endif; ?>
    </script>
</body>
</html>