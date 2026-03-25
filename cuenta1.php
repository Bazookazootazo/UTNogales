<?php 
$pagina_actual = 'cuanta';
require_once 'config/auth.php';
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>MTB Sistema — Mi Cuenta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/mtb-dashboard.css" />
    <style>
        .sidebar-user { display: flex; align-items: center; padding: 12px; border-radius: 8px; transition: all 0.3s ease; cursor: pointer; color: white; text-decoration: none; }
        .sidebar-user.active { background-color: rgba(255, 107, 0, 0.15); border-left: 3px solid #ff6b00; }
        .sidebar-user.active .user-name { color: #ff6b00; font-weight: bold; }
        .logout-icon { color: rgba(255,255,255,.4); transition: color .2s; margin-left: auto; }
        .logout-icon:hover { color: #ff4444; }

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
    <div class="sidebar-user">
        <div class="user-avatar"><?php echo $iniciales; ?></div>
        
        <a href="cuenta.php" class="user-info">
            <div class="user-name"><?php echo $nombre_completo; ?></div>
            <div class="user-role"><?php echo $rol; ?></div>
        </a>
        
        <a href="cerrarSesion.php" title="Cerrar sesión" style="color:rgba(255,255,255,.4); transition: color .2s;">
            <i class="fas fa-right-from-bracket"></i>
        </a>
    </div>
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
    <div class="perfil-card" style="background: white; border-radius: 12px; width: 100%; max-width: 1000px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #eee;">
        
        <div style="background-color: #2f3430; padding: 1.5em; text-align: center; border-bottom: 4px solid #ff6b00;">
            <i class="fas fa-user-circle" style="font-size: 4em; color: #ff6b00; margin-bottom: 5px;"></i>
            <h3 style="color: white; margin: 0; font-size: 1.6em;"><?php echo $nombre_completo; ?></h3>
            <span style="background: #ff6b00; padding: 3px 15px; border-radius: 20px; font-size: 0.75em; font-weight: bold; color: white; text-transform: uppercase; margin-top: 8px; display: inline-block;">
                <?php echo $rol; ?>
            </span>
        </div>   

        <div style="padding: 1.5em 2.5em; background: white;">
            
            <div style="display: flex; justify-content: space-between; gap: 20px; margin-bottom: 1.5em; border-bottom: 1px solid #f5f5f5; padding-bottom: 15px;">
                
                <div style="flex: 0 0 25%;">
                    <label style="font-weight: bold; color: #999; font-size: 0.7em; text-transform: uppercase; letter-spacing: 0.5px; display: block;">
                        <i class="fas fa-user" style="color: #ff6b00; margin-right: 5px;"></i> Nombre
                    </label>
                    <p style="margin: 5px 0 0; font-size: 1.05em; color: #333; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?php echo $nombre_completo; ?>
                    </p>
                </div>

                <div style="flex: 0 0 45%;">
                    <label style="font-weight: bold; color: #999; font-size: 0.7em; text-transform: uppercase; letter-spacing: 0.5px; display: block;">
                        <i class="fas fa-envelope" style="color: #ff6b00; margin-right: 5px;"></i> Correo Electrónico
                    </label>
                    <p style="margin: 5px 0 0; font-size: 1.05em; color: #333; font-weight: 500; white-space: nowrap;">
                        <?php echo $correo; ?>
                    </p>
                </div>

                <div style="flex: 0 0 20%;">
                    <label style="font-weight: bold; color: #999; font-size: 0.7em; text-transform: uppercase; letter-spacing: 0.5px; display: block;">
                        <i class="fas fa-phone" style="color: #ff6b00; margin-right: 5px;"></i> Teléfono
                    </label>
                    <p style="margin: 5px 0 0; font-size: 1.05em; color: #333; font-weight: 500;">
                        <?php echo !empty($telefono) ? $telefono : '---'; ?>
                    </p>
                </div>
            </div>

            <div style="display: flex; gap: 40px; margin-bottom: 1.5em;">
                <div>
                    <label style="font-weight: bold; color: #999; font-size: 0.7em; text-transform: uppercase; display: block;">
                        <i class="fas fa-user-tag" style="color: #ff6b00; margin-right: 5px;"></i> Rol
                    </label>
                    <p style="margin: 5px 0 0; font-size: 1em; color: #333; font-weight: 500;"><?php echo $rol; ?></p>
                </div>
                <div>
                    <label style="font-weight: bold; color: #999; font-size: 0.7em; text-transform: uppercase; display: block;">
                        <i class="fas fa-check-circle" style="color: #28a745; margin-right: 5px;"></i> Estatus
                    </label>
                    <p style="margin: 5px 0 0; font-size: 1em; color: #28a745; font-weight: bold;">Activo</p>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="editar_cuenta.php" style="text-decoration: none; background: #ff6b00; color: white; padding: 8px 20px; border-radius: 6px; font-size: 0.9em; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit"></i> Editar Datos
                </a>
                <a href="eliminar_cuenta.php" onclick="return confirm('¿Borrar cuenta?')" style="text-decoration: none; background: #fff; color: #dc3545; border: 1.5px solid #dc3545; padding: 7px 20px; border-radius: 6px; font-size: 0.9em; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-trash-alt"></i> Borrar Cuenta
                </a>
            </div>
        </div>
    </div>
</div>
    </main>
<script>
    // JS para el sidebar
    const toggleBtn = document.getElementById('toggleSidebar');
    if(toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            document.getElementById('mtbSidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        });
    }
</script>

</body>
</html>