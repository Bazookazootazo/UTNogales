<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>MTB Sistema — Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/mtb-dashboard.css" />
</head>
<body>

<div class="mtb-app">
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="mtb-sidebar" id="mtbSidebar">

    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="fas fa-person-biking"></i>
        </div>
        <div class="sidebar-brand-text">
            <span class="brand-name">MTB</span>
            <span class="brand-sub">Mountain Bike System</span>
        </div>
    </div>

    <nav class="sidebar-nav">

        <span class="nav-section-label">Principal</span>

        <div class="nav-item">
            <a href="inicio.php" class="nav-link <?php echo (isset($pagina_actual) && $pagina_actual == 'inicio') ? 'active' : ''; ?>">
                <span class="nav-icon"><i class="fas fa-th-large"></i></span>
                <span class="nav-label">Dashboard</span>
            </a>
        </div>

        <div class="nav-divider"></div>

        <span class="nav-section-label">Gestión</span>

        <div class="nav-item">
            <a href="#" class="nav-link <?php echo (isset($pagina_actual) && $pagina_actual == 'eventos') ? 'active' : ''; ?>" data-page="eventos">
                <span class="nav-icon"><i class="fas fa-calendar-days"></i></span>
                <span class="nav-label">Eventos y Fechas</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="archivo2.php" class="nav-link <?php echo (isset($pagina_actual) && $pagina_actual == 'inscripciones') ? 'active' : ''; ?>">
                <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
                <span class="nav-label">Inscripciones</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="pistas.php" class="nav-link <?php echo (isset($pagina_actual) && $pagina_actual == 'pistas') ? 'active' : ''; ?>" data-page="pistas">
                <span class="nav-icon"><i class="fas fa-map-location-dot"></i></span>
                <span class="nav-label">Pistas</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="#" class="nav-link <?php echo (isset($pagina_actual) && $pagina_actual == 'deportistas') ? 'active' : ''; ?>" data-page="deportistas">
                <span class="nav-icon"><i class="fas fa-users"></i></span>
                <span class="nav-label">Deportistas</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="#" class="nav-link <?php echo (isset($pagina_actual) && $pagina_actual == 'categorias') ? 'active' : ''; ?>" data-page="categorias">
                <span class="nav-icon"><i class="fas fa-layer-group"></i></span>
                <span class="nav-label">Categorías</span>
            </a>
        </div>

        <div class="nav-divider"></div>

        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'ADMIN'): ?>
            <span class="nav-section-label">Administración</span>

            <div class="nav-item">
                <a href="administracion_de_usuarios.php" class="nav-link <?php echo (isset($pagina_actual) && $pagina_actual == 'AdministracionUsuarios') ? 'active' : ''; ?>" data-page="usuarios">
                    <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
                    <span class="nav-label">Usuarios</span>
                </a>
            </div>
        <?php endif; ?>

    </nav>

    <div class="sidebar-footer">
        <a href="cuenta.php" class="sidebar-user <?php echo (isset($pagina_actual) && $pagina_actual == 'cuenta') ? 'active' : ''; ?>" style="text-decoration: none;">
            
            <div class="user-avatar"><?php echo isset($iniciales) ? $iniciales : 'U'; ?></div>
            
            <div class="user-info">
                <div class="user-name"><?php echo isset($nombre_completo) ? $nombre_completo : 'Usuario'; ?></div>
                <div class="user-role"><?php echo isset($rol) ? $rol : 'Rol'; ?></div>
            </div>
            
            <object>
                <a href="#" onclick="confirmarCierreSesion(event)" title="Cerrar sesión" class="logout-icon" style="color:rgba(255,255,255,.4); transition: color .2s;">
                    <i class="fas fa-right-from-bracket"></i>
                </a>
            </object>
        </a>
    </div>

</aside>