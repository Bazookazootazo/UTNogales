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
            <div class="nav-item">
                <a href="inscripciones.php" class="nav-link <?php echo ($pagina_actual == 'inscripciones') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
                    <span class="nav-label">Inscripciones</span>
                </a>
            </div>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'ADMIN'): ?>
<div class="nav-divider"></div>
<span class="nav-section-label">Administración</span>
<div class="nav-item">
    <a href="administracion_de_usuarios.php" class="nav-link <?php echo ($pagina_actual == 'AdministracionUsuarios') ? 'active' : ''; ?>">
        <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
        <span class="nav-label">Usuarios</span>
    </a>
</div>
<?php endif; ?>
        </nav>

        <div class="sidebar-footer">
    <div class="sidebar-user <?php echo ($pagina_actual == 'cuenta') ? 'active' : ''; ?>">
        <div class="user-avatar">
            <?php echo isset($iniciales) ? $iniciales : 'U'; ?>
        </div>

        <a href="cuenta.php" class="user-info">
            <div class="user-name">
                <?php echo isset($nombre_completo) ? htmlspecialchars($nombre_completo) : 'Usuario'; ?>
            </div>
            <div class="user-role">
                <?php echo isset($rol) ? htmlspecialchars($rol) : 'Rol'; ?>
            </div>
        </a>

        <a href="#" onclick="confirmarCierreSesion(event)" title="Cerrar sesión" style="color:rgba(255,255,255,.4); cursor:pointer;">
            <i class="fas fa-right-from-bracket"></i>
        </a>
    </div>
</div>

    </aside>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarCierreSesion(event) {
    event.preventDefault(); // Evita que la página salte al inicio (#)

    Swal.fire({
        title: '¿Cerrar sesión?',
        text: "Tendrás que volver a ingresar tus credenciales.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ff6b00',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // USAMOS RUTA RELATIVA SEGURA
            // Esto funcionará siempre que estés en la raíz (inicio.php, cuenta.php, etc.)
            window.location.href = "actions/cerrarSesion.php";
        }
    });
}
</script>