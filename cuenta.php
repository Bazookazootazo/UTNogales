<?php 
$pagina_actual = 'cuenta'; 
session_start();
include 'conexion.php'; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: registro.php");
    exit();
}

$id_logueado = $_SESSION['id_usuario'];

try {
   $query_user = "SELECT nombreUser, apellidosUser, correoUser, telefonoUser, rol, ultimoAcceso, estatus FROM usuarios WHERE numeroUser = ?";
$stmt = $conn->prepare($query_user);
$stmt->execute([$id_logueado]);
$datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($datos_usuario) {
    $nombre_completo = $datos_usuario['nombreUser'] . " " . $datos_usuario['apellidosUser'];
    $correo = $datos_usuario['correoUser'];
    $telefono = $datos_usuario['telefonoUser'];
    $rol = $datos_usuario['rol'];
    $estatus = $datos_usuario['estatus'];
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
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>MTB Sistema — Mi Cuenta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="mtb-dashboard.css" />
    <style>
        /* Estilo base del footer user */
.sidebar-user {
    display: flex;
    align-items: center;
    padding: 12px;
    border-radius: var(--radius-md);
    transition: all 0.3s ease;
    cursor: pointer;
    color: white; /* O el color de tu texto */
}

/* Estado activo (Naranja) */
.sidebar-user.active {
    background-color: rgba(255, 107, 0, 0.15); /* Fondo naranja suave */
    border-left: 3px solid #ff6b00; /* Línea naranja intensa */
}

.sidebar-user.active .user-name {
    color: #ff6b00; /* Texto del nombre en naranja */
    font-weight: bold;
}

.logout-icon {
    color: rgba(255,255,255,.4);
    transition: color .2s;
    margin-left: auto;
}

.logout-icon:hover {
    color: #ff4444; /* Rojo al pasar el mouse por cerrar sesión */
}

        /* FIX: Forzamos el layout de la app para que no centre la barra */
        .mtb-app {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Contenedor principal de la derecha */
        .mtb-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background-color: #f4f7f6;
        }

        /* Área de la tarjeta con centrado independiente */
        .perfil-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
    </style>
</head>
<body>

<div class="mtb-app">

    <!-- ── OVERLAY para móvil (cierra el sidebar) ── -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ════════════════════════════════════════════════
         SIDEBAR
    ════════════════════════════════════════════════ -->
    <aside class="mtb-sidebar" id="mtbSidebar">

        <!-- Marca / Logo -->
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-person-biking"></i>
            </div>
            <div class="sidebar-brand-text">
                <span class="brand-name">MTB</span>
                <span class="brand-sub">Mountain Bike System</span>
            </div>
        </div>

        <!-- Navegación -->
        <nav class="sidebar-nav">

            <!-- Principal -->
            <span class="nav-section-label">Principal</span>

            <div class="nav-item">
               <a href="inicio.php" class="nav-link <?php echo ($pagina_actual == 'inicio') ? 'active' : ''; ?>">
    <span class="nav-icon"><i class="fas fa-th-large"></i></span>
    <span class="nav-label">Dashboard</span>
</a>
            </div>

            <div class="nav-divider"></div>

            <!-- Gestión -->
            <span class="nav-section-label">Gestión</span>

            <div class="nav-item">
                <a href="#" class="nav-link" data-page="eventos">
                    <span class="nav-icon"><i class="fas fa-calendar-days"></i></span>
                    <span class="nav-label">Eventos y Fechas</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="archivo2.php" class="nav-link <?php echo ($pagina_actual == 'inscripciones') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
                    <span class="nav-label">Inscripciones</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="pistas.php" class="nav-link" data-page="pistas">
                    <span class="nav-icon"><i class="fas fa-map-location-dot"></i></span>
                    <span class="nav-label">Pistas</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="#" class="nav-link" data-page="deportistas">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    <span class="nav-label">Deportistas</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="#" class="nav-link" data-page="categorias">
                    <span class="nav-icon"><i class="fas fa-layer-group"></i></span>
                    <span class="nav-label">Categorías</span>
                </a>
            </div>

            <div class="nav-divider"></div>

            <!-- Resultados -->
            <span class="nav-section-label">Resultados</span>

            <div class="nav-item">
                <a href="#" class="nav-link" data-page="resultados">
                    <span class="nav-icon"><i class="fas fa-trophy"></i></span>
                    <span class="nav-label">Resultados</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="#" class="nav-link" data-page="ranking">
                    <span class="nav-icon"><i class="fas fa-ranking-star"></i></span>
                    <span class="nav-label">Ranking General</span>
                </a>
            </div>

            <div class="nav-divider"></div>

            <!-- Administración -->
            <?php if ($_SESSION['rol'] == 'ADMIN'): ?>
            <span class="nav-section-label">Administración</span>

            <div class="nav-item">
                <a href="#" class="nav-link" data-page="usuarios">
                    <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
                    <span class="nav-label">Usuarios</span>
                </a>
            </div>
        </nav>
    <?php endif; ?>

        <!-- Footer del Sidebar -->
        <div class="sidebar-footer">
    <a href="cuenta.php" class="sidebar-user <?php echo ($pagina_actual == 'cuenta') ? 'active' : ''; ?>" style="text-decoration: none;">
        
        <div class="user-avatar"><?php echo $iniciales; ?></div>
        
        <div class="user-info">
            <div class="user-name"><?php echo $nombre_completo; ?></div>
            <div class="user-role"><?php echo $rol; ?></div>
        </div>
        
        <object>
            <a href="cerrarSesion.php" title="Cerrar sesión" class="logout-icon">
                <i class="fas fa-right-from-bracket"></i>
            </a>
        </object>
    </a>
</div>

    </aside>

    <main class="mtb-content">
        
        <header class="mtb-topbar" style="background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0e0e0;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="text-align: left;">
                    <div class="topbar-title">Mi cuenta</div>
                    <div style="font-size: 0.85rem; color: #888;">Configuración › Perfil de usuario</div>
                </div>
            </div>
        </header>

        <div class="perfil-container" style="display: flex; justify-content: center; padding: 10px;">
    <div class="perfil-card" style="background: white; border-radius: 12px; height: 100%; max-height: 500px; width: 100%; max-width: 950px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #eee;">
        
        <div style="background-color: #2f3430; padding: 1.2em; text-align: center; border-bottom: 4px solid #ff6b00;">
            <i class="fas fa-user-circle" style="font-size: 3.5em; color: #ff6b00; margin-bottom: 5px;"></i>
            <h3 style="color: white; margin: 0; font-size: 1.5em;"><?php echo $nombre_completo; ?></h3>
            <span style="background: #ff6b00; padding: 2px 12px; border-radius: 20px; font-size: 0.7em; font-weight: bold; color: white; text-transform: uppercase; margin-top: 5px; display: inline-block;">
                <?php echo $rol; ?>
            </span>
        </div>   

        <div style="padding: 1.5em 3em; background: white;">
            
            <div style="display: flex; width: 100%; gap: 10px; border-bottom: 1px solid #f5f5f5; padding-bottom: 15px; margin-bottom: 15px;">
                
                <div style="width: 30%;">
                    <label style="font-weight: bold; color: #999; font-size: 0.7em; text-transform: uppercase; display: block;">
                        <i class="fas fa-user" style="color: #ff6b00; margin-right: 5px;"></i> Nombre
                    </label>
                    <p style="margin: 5px 0 0; font-size: 1em; color: #333; font-weight: 500;"><?php echo $nombre_completo; ?></p>
                </div>

                <div style="width: 45%;">
                    <label style="font-weight: bold; color: #999; font-size: 0.7em; text-transform: uppercase; display: block;">
                        <i class="fas fa-envelope" style="color: #ff6b00; margin-right: 5px;"></i> Correo Electrónico
                    </label>
                    <p style="margin: 5px 0 0; font-size: 1em; color: #333; font-weight: 500;"><?php echo $correo; ?></p>
                </div>

                <div style="width: 25%;">
                    <label style="font-weight: bold; color: #999; font-size: 0.7em; text-transform: uppercase; display: block;">
                        <i class="fas fa-phone" style="color: #ff6b00; margin-right: 5px;"></i> Teléfono
                    </label>
                    <p style="margin: 5px 0 0; font-size: 1em; color: #333; font-weight: 500;">
                        <?php echo !empty($telefono) ? $telefono : '---'; ?>
                    </p>
                </div>
            </div>

            <div style="display: flex; width: 100%; gap: 10px;">
                <div style="width: 30%;">
                    <label style="font-weight: bold; color: #999; font-size: 0.7em; text-transform: uppercase; display: block;">
                        <i class="fas fa-user-tag" style="color: #ff6b00; margin-right: 5px;"></i> Rol
                    </label>
                    <p style="margin: 5px 0 0; font-size: 0.95em; color: #333; font-weight: 500;"><?php echo $rol; ?></p>
                </div>
                <div style="width: 45%;">
                    <label style="font-weight: bold; color: #999; font-size: 0.7em; text-transform: uppercase; display: block;">
                        <i class="fas fa-check-circle" style="color: #28a745; margin-right: 5px;"></i> Estatus
                    </label>
                    <p style="margin: 5px 0 0; font-size: 0.95em; color: #28a745; font-weight: bold;"><?php echo $estatus; ?></p></p>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 5px; padding-top: 120px;">
    <button onclick="abrirModalEditar()" button class="btn btn-primary btn-sm" style="background: #ff6b00; color: white; padding: 7px 18px; border-radius: 6px; font-size: 0.85em; font-weight: bold; display: flex; align-items: center; gap: 6px; text-decoration: none;">
        <i class="fas fa-edit"></i> Editar Datos
            </button>

    <button onclick="confirmarEliminar('<?php echo $id_logueado; ?>', '<?php echo $rol; ?>')" 
         class="btn btn-primary btn-sm" style="background: #fff; color: #dc3545; border: 1.5px solid #dc3545; padding: 6px 18px; border-radius: 6px; font-size: 0.85em; font-weight: bold; display: flex; align-items: center; gap: 6px; cursor: pointer;">
        <i class="fas fa-trash-alt"></i> Borrar Cuenta
    </button>
</div>
        </div>
    </div>
</div>
</main>

<!-- ══════════════════════════════════════════════════════════
     modal:editar usuarios
══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalEditarUsuarios">
    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-user"></i> Modificar perfil</h2>
            <button class="modal-close" onclick="cerrarModal('modalEditarUsuarios')">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formEvento">
                <div class="form-group">
                    <label class="form-label required">Nombre del Evento</label>
                    <input type="text" class="form-control" placeholder="Ej. Enduro Copa MTB Nogales 2026" required>
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
                    <textarea class="form-control" placeholder="Modalidad, desnivel, distancia..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal('modalEditarUsuarios')">Cancelar</button>
            <button class="btn btn-primary" onclick="cerrarModal('modalEditarUsuarios'); showToast('Usuario editado correctamente', 'success')">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const toggleBtn = document.getElementById('toggleSidebar');
    if(toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            document.getElementById('mtbSidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        });
    }
function confirmarEliminar(id, rol) {
    console.log("ID recibido:", id, "Rol recibido:", rol);

    if (rol === 'ADMIN') {
        Swal.fire({
            title: 'No se puede realizar ese movimiento',
            text: 'Como Administrador, no puedes eliminar tu propia cuenta por seguridad.',
            icon: 'error',
            confirmButtonColor: '#ff6b00'
        });
        return; 
    }

    Swal.fire({
        title: '¿Estás seguro?',
        text: "Tu cuenta será Desactivada. Podras reactivarla en un lapzo de 30 dias.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff6b00',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `dar_de_baja_cuenta.php?numeroUser=${id}`;
        }
    });
}

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

function abrirModalEditar() { abrirModal('modalEditarUsuarios'); }

</script>
</body>
</html>