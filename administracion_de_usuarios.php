<?php $pagina_actual = 'AdministracionUsuarios';
session_start();
include 'conexion.php'; 
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
.logout-icon {
    color: rgba(255,255,255,.4);
    transition: color .2s;
    margin-left: auto;
}

.logout-icon:hover {
    color: #ff4444; 
}
        .mtb-app {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        .mtb-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background-color: #f4f7f6;
        }

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

            <!-- Administración -->
            <?php if ($_SESSION['rol'] == 'ADMIN'): ?>
            <span class="nav-section-label">Administración</span>

    <div class="nav-item">
        <a href="administracion_de_usuarios.php" 
           class="nav-link <?php echo ($pagina_actual == 'AdministracionUsuarios') ? 'active' : ''; ?>" 
           data-page="usuarios">
            <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
            <span class="nav-label">Usuarios</span>
        </a>
    </div>
    <?php endif; ?>

        <!-- Footer del Sidebar -->
        <div class="sidebar-footer">
    <a href="cuenta.php" class="sidebar-user" style="text-decoration: none;">
        
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
</script>
</body>
</html>