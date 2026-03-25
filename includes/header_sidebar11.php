<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>MTB</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/mtb-dashboard.css" />
</head>
<body>

<div class="mtb-app">
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="mtb-sidebar" id="mtbSidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="fas fa-person-biking"></i></div>
        <div class="sidebar-brand-text">
            <span class="brand-name">MTB</span>
            <span class="brand-sub">Mountain Bike System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section-label">Principal</span>
        <div class="nav-item">
            <a href="inicio.php" class="nav-link <?php echo ($pagina_actual == 'inicio') ? 'active' : ''; ?>">
                <span class="nav-icon"><i class="fas fa-th-large"></i></span>
                <span class="nav-label">Dashboard</span>
            </a>
        </div>

        <div class="nav-divider"></div>
        <span class="nav-section-label">Gestión</span>
        <?php if ($_SESSION['rol'] == 'ADMIN'): ?>
        <div class="nav-divider"></div>
        <span class="nav-section-label">Administración</span>
        <div class="nav-item">
            <a href="administracion_de_usuarios.php" class="nav-link <?php echo ($pagina_actual == 'usuarios') ? 'active' : ''; ?>">
                <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
                <span class="nav-label">Usuarios</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="cuenta.php" class="sidebar-user <?php echo ($pagina_actual == 'cuenta') ? 'active' : ''; ?>" style="text-decoration: none;">
            <div class="user-avatar"><?php echo $iniciales; ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo $nombre_completo; ?></div>
                <div class="user-role"><?php echo $rol; ?></div>
            </div>
            <object>
                <a href="actions/cerrarSesion.php" title="Cerrar sesión" class="logout-icon">
                    <i class="fas fa-right-from-bracket"></i>
                </a>
            </object>
        </a>
    </div>
</aside>