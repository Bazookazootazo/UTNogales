<?php $pagina_actual = 'pistas'; ?>
<?php
session_start();
include 'config/conexion.php'; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: registro.php");
    exit();
}

$id_logueado = $_SESSION['id_usuario'];
try {
    $query_user = "SELECT nombreUser, apellidosUser, rol, estatus FROM usuarios WHERE numeroUser = ?";
    $stmt = $conn->prepare($query_user);
    $stmt->execute([$id_logueado]);
    $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($datos_usuario && strtoupper($datos_usuario['estatus']) !== 'INACTIVO') {
        $nombre_completo = $datos_usuario['nombreUser'] . " " . $datos_usuario['apellidosUser'];
        $rol = $datos_usuario['rol'];
    } else {
        session_destroy();
        header("Location: index.php?error=" . urlencode("Tu sesión ha expirado o tu cuenta ha sido desactivada."));
        exit();
    }
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}

// Cargar datos según el rol usando las vistas que creamos
try {
    if ($rol === 'ADMIN') {
        $stmt_pistas = $conn->query("SELECT * FROM vw_pistas_admin");
    } else {
        $stmt_pistas = $conn->query("SELECT * FROM vw_pistas_cliente");
    }
    $pistas = $stmt_pistas->fetchAll(PDO::FETCH_ASSOC);
    
    // Contadores para las tarjetas de estadísticas
    $total_pistas = count($pistas);
    $abiertas = 0; $mantenimiento = 0;
    foreach($pistas as $p) {
        if ($p['estadoPista'] === 'Abierta') $abiertas++;
        if ($p['estadoPista'] === 'En mantenimiento') $mantenimiento++;
    }

} catch (PDOException $e) {
    $pistas = [];
    $total_pistas = 0; $abiertas = 0; $mantenimiento = 0;
}
?>

<?php include_once 'includes/header_sidebar.php'; ?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Pistas</title>
    <link rel="stylesheet" href="assets/css/mtb-dashboard.css">
    <style>
        /* Estilos específicos para las tarjetas del catálogo del cliente */
        .pistas-grid-cliente {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        .card-pista {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            border: 1px solid #eee;
        }
        .card-pista:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .card-pista-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-bottom: 3px solid var(--mtb-primary);
        }
        .card-pista-body { padding: 20px; }
        .card-pista-title { font-family: var(--font-display); font-size: 1.4rem; color: var(--mtb-dark); margin: 0 0 10px 0; }
        
        /* Badges de Estado */
        .estado-badge { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: bold; display: inline-flex; align-items: center; gap: 5px; }
        .estado-abierta { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .estado-mantenimiento { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .estado-cerrada { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="mtb-content">
        <header class="mtb-topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="toggleSidebar"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="topbar-title">Catálogo de Pistas</div>
                    <div class="topbar-breadcrumb">Pistas › Resumen general</div>
                </div>
            </div>
            <div class="topbar-right">
                <?php if ($rol === 'ADMIN'): ?>
                <button class="topbar-action-btn" onclick="abrirModalPista()">
                    <i class="fas fa-plus"></i> Registrar nueva pista
                </button>
                <?php endif; ?>
                <button class="topbar-icon-btn"><i class="fas fa-bell"></i><span class="topbar-badge"></span></button>
            </div>
        </header>

        <main class="mtb-inner">
            <div class="mtb-stats-grid">
                <div class="stat-card" style="border-left: 4px solid var(--mtb-primary);">
                    <div class="stat-icon" style="background: rgba(232, 99, 10, 0.1); color: var(--mtb-primary);"><i class="fas fa-route"></i></div>
                    <div class="stat-details">
                        <h3><?= $total_pistas ?></h3><p>Pistas Registradas</p>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #28a745;">
                    <div class="stat-icon" style="background: rgba(40, 167, 69, 0.1); color: #28a745;"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-details">
                        <h3><?= $abiertas ?></h3><p>Pistas Abiertas</p>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #ffc107;">
                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;"><i class="fas fa-tools"></i></div>
                    <div class="stat-details">
                        <h3><?= $mantenimiento ?></h3><p>En Mantenimiento</p>
                    </div>
                </div>
            </div>

            <?php if ($rol === 'ADMIN'): ?>
            <section class="tabla-seccion" style="margin-top: 20px;">
                <div class="tabla-card">
                    <div class="tabla-header">
                        <h3 style="margin:0; font-size: 1.1rem;"><i class="fas fa-map-marked-alt" style="margin-right:10px;"></i> Gestión de Pistas</h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>ID</th>
                                    <th>Nombre de Pista</th>
                                    <th>Sección</th>
                                    <th>Estado Físico</th>
                                    <th style="text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pistas as $p): ?>
                                <tr>
                                    <td><img src="assets/img/pistas/<?= $p['rutaImagen'] ?>" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;"></td>
                                    <td style="font-weight: bold; color: #ff6b00;">#<?= $p['numeroPista'] ?></td>
                                    <td style="font-weight: 600;"><?= htmlspecialchars($p['nombrePista']) ?></td>
                                    <td><?= htmlspecialchars($p['seccion']) ?></td>
                                    <td>
                                        <?php 
                                            if($p['estadoPista'] == 'Abierta') echo '<span class="estado-badge estado-abierta"><i class="fas fa-check"></i> Abierta</span>';
                                            elseif($p['estadoPista'] == 'En mantenimiento') echo '<span class="estado-badge estado-mantenimiento"><i class="fas fa-tools"></i> Mantenimiento</span>';
                                            else echo '<span class="estado-badge estado-cerrada"><i class="fas fa-times"></i> Cerrada</span>';
                                        ?>
                                    </td>
                                    <td style="text-align: center; display: flex; gap: 8px; justify-content: center;">
                                        <a href="#" class="btn-accion btn-actualizar" style="border: 1px solid #ff6b00;" 
                                           onclick="abrirModalEditarPista(<?= htmlspecialchars(json_encode($p)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <?php if($p['estatus'] == 'ACTIVO'): ?>
                                            <a href="actions/estatus_pista.php?id=<?= $p['numeroPista'] ?>&estado=INACTIVO" class="btn-accion btn-desactivar" title="Desactivar" style="border: 1px solid #666;">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="actions/estatus_pista.php?id=<?= $p['numeroPista'] ?>&estado=ACTIVO" class="btn-accion btn-reactivar" title="Reactivar" style="border: 1px solid #28a745;">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
            <h2 style="margin-top: 30px; font-family: var(--font-display); color: var(--mtb-dark);">Explora nuestros circuitos</h2>
            <div class="pistas-grid-cliente">
                <?php foreach ($pistas as $p): ?>
                <div class="card-pista">
                    <img src="assets/img/pistas/<?= $p['rutaImagen'] ?>" class="card-pista-img" alt="<?= $p['nombrePista'] ?>" onerror="this.src='assets/img/pistas/default_pista.png'">
                    <div class="card-pista-body">
                        <h3 class="card-pista-title"><?= htmlspecialchars($p['nombrePista']) ?></h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 15px;"><i class="fas fa-map-marker-alt" style="color: #ff6b00;"></i> <?= htmlspecialchars($p['seccion']) ?></p>
                        
                        <?php 
                            if($p['estadoPista'] == 'Abierta') echo '<span class="estado-badge estado-abierta"><i class="fas fa-check"></i> Disponible</span>';
                            elseif($p['estadoPista'] == 'En mantenimiento') echo '<span class="estado-badge estado-mantenimiento"><i class="fas fa-tools"></i> En mantenimiento</span>';
                            else echo '<span class="estado-badge estado-cerrada"><i class="fas fa-times"></i> Temporalmente Cerrada</span>';
                        ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </main>
    </div>

    <?php if ($rol === 'ADMIN'): ?>
    
    <div class="modal-overlay" id="modalPista">
        <div class="modal">
            <div class="modal-header">
                <h2><i class="fas fa-route"></i> Registrar Nueva Pista</h2>
                <button class="modal-close" onclick="cerrarModal('modalPista')"><i class="fas fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form id="formPista" onsubmit="event.preventDefault(); guardarPista();" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label required">Nombre de la Pista</label>
                        <input type="text" class="form-control" name="nombrePista" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Sección / Ubicación</label>
                        <input type="text" class="form-control" name="seccion" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Estado Físico Actual</label>
                        <select name="estadoPista" class="form-control" required>
                            <option value="Abierta">Abierta (Disponible)</option>
                            <option value="En mantenimiento">En Mantenimiento</option>
                            <option value="Cerrada">Cerrada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Imagen de la pista</label>
                        <input type="file" class="form-control" name="imagen_archivo" accept="image/*">
                    </div>
                    <div class="modal-footer" style="padding: 20px 0 0 0;">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalPista')">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Pista</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalEditarPista">
        <div class="modal">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Modificar Pista</h2>
                <button class="modal-close" onclick="cerrarModal('modalEditarPista')"><i class="fas fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form id="formEditarPista" method="POST" action="actions/actualizar_pista.php" enctype="multipart/form-data">
                    <input type="hidden" name="numeroPista" id="edit_numeroPista">
                    <div class="form-group">
                        <label class="form-label required">Nombre</label>
                        <input type="text" name="nombrePista" id="edit_nombrePista" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Sección</label>
                        <input type="text" name="seccion" id="edit_seccion" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Estado</label>
                        <select name="estadoPista" id="edit_estadoPista" class="form-control" required>
                            <option value="Abierta">Abierta</option>
                            <option value="En mantenimiento">En mantenimiento</option>
                            <option value="Cerrada">Cerrada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Actualizar Imagen (Opcional)</label>
                        <input type="file" name="imagen_archivo" class="form-control" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarPista')">Cancelar</button>
                <button type="submit" form="formEditarPista" class="btn btn-primary" style="background: #ff6b00; border: none;"><i class="fas fa-save"></i> Actualizar</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function abrirModal(id) { document.getElementById(id).classList.add('active'); }
    function cerrarModal(id) { document.getElementById(id).classList.remove('active'); }

    function abrirModalPista() { abrirModal('modalPista'); }
    
    function abrirModalEditarPista(pista) {
        document.getElementById('edit_numeroPista').value = pista.numeroPista;
        document.getElementById('edit_nombrePista').value = pista.nombrePista;
        document.getElementById('edit_seccion').value = pista.seccion;
        document.getElementById('edit_estadoPista').value = pista.estadoPista;
        abrirModal('modalEditarPista');
    }

    // Petición AJAX para guardar pista
    function guardarPista() {
        const formData = new FormData(document.getElementById('formPista'));
        fetch('actions/guardar_pista.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            cerrarModal('modalPista');
            if (data.status === 'success') {
                Swal.fire({ icon: 'success', title: '¡Registrado!', text: data.message, showConfirmButton: false, timer: 1500 }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'warning', title: 'Atención', text: data.message });
            }
        });
    }

    // Detectar alertas de redirección (Actualizar/Estatus)
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('msg') === 'actualizado_ok') {
            Swal.fire({ title: '¡Actualizado!', text: 'La pista se guardó correctamente.', icon: 'success', confirmButtonColor: '#E8630A' }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });
    </script>
</body>
</html>