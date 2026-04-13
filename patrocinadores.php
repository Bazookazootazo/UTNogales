<?php $pagina_actual = 'patrocinadores'; ?>
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

try {
    $query = $conn->query("SELECT logo_patrocinador, nombrePatrocinador FROM patrocinador");
    $patrocinadores = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $patrocinadores = [];
}

try {

    $query_lista = "SELECT numeroPatrocinador, nombrePatrocinador, contactoPatrocinador, logo_patrocinador, estatus FROM patrocinador";
    $stmt_lista = $conn->prepare($query_lista);
    $stmt_lista->execute();
    $patrocinadores_lista = $stmt_lista->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>

<?php include_once 'includes/header_sidebar.php'; ?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patrocinadores</title>
    <link rel="stylesheet" href="assets/css/mtb-dashboard.css">
<style>
html, body {
    max-width: 100% !important;
    overflow-x: hidden !important;
}

.mtb-content {
    width: 100% !important;
    max-width: 100vw !important;
    overflow-x: hidden !important;
    display: flex;
    flex-direction: column;
}

.mtb-topbar {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 0 20px !important;
    height: 70px !important;
    width: 100% !important;
    position: sticky;
    top: 0;
    z-index: 1000;
    background: #fff;
}

.topbar-right {
    display: flex !important;
    align-items: center !important;
    gap: 15px !important;
    flex-shrink: 0 !important;
}

/* 2. EL CONTENEDOR (CUADRO): Añadimos borde sutil y mejor sombra */
.slider-patrocinadores {
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
    background: #fff;
    padding: 40px 0;
    position: relative; 
    margin: 20px 0;
    border-radius: 18px; /* Un poco más redondeado */
    box-shadow: 0 10px 35px rgba(0,0,0,0.05);
    border: 1px solid rgba(0,0,0,0.05); /* Detalle de borde fino */
}

/* 3. EL RIELE: Alineación vertical perfecta */
.slider-track {
    display: flex !important;
    align-items: center !important; /* Alinea los logos al mismo nivel vertical */
    width: max-content !important; 
    animation: scroll 25s linear infinite !important;
}

/* 4. LOS LOGOS: Altura uniforme y efectos */
.slide {
    width: 250px !important; 
    flex-shrink: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.slide img {
    max-width: 180px;
    max-height: 85px !important; /* Altura máxima para que todos se vean nivelados */
    width: auto;
    height: auto;
    object-fit: contain;
    transition: transform 0.3s ease, opacity 0.3s ease;
    opacity: 0.9;
}

.slide img:hover {
    transform: scale(1.1);
    opacity: 1;
}

.slider-patrocinadores::before,
.slider-patrocinadores::after {
    content: "";
    height: 100%;
    position: absolute;
    width: 150px; /* Desvanecimiento más amplio */
    z-index: 2;
    pointer-events: none;
    top: 0;
}

.slider-patrocinadores::before {
    left: 0;
    background: linear-gradient(to right, #fff 10%, rgba(255, 255, 255, 0) 100%);
}

.slider-patrocinadores::after {
    right: 0;
    background: linear-gradient(to left, #fff 10%, rgba(255, 255, 255, 0) 100%);
}
/* 6. ANIMACIÓN Y ESTADÍSTICAS */
@keyframes scroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* Pausar al pasar el mouse */
.slider-track:hover {
    animation-play-state: paused;
}

/* Estilo para las tarjetas superiores */
.mtb-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    border-left: 4px solid var(--mtb-primary, #ff8800); /* Detalle de color lateral */
}
</style>
</style>
</head>
<body>
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
                    <div class="topbar-title">Patrocinadores</div>
                    <div class="topbar-breadcrumb">Patrocinadores › Resumen general</div>
                </div>
            </div>
            <div class="topbar-right">
                <button class="topbar-action-btn" onclick="abrirModalPatrocinador()">
                    <i class="fas fa-plus"></i> Registrar nuevo patrocinador
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
        <main class = "mtb-inner">
<?php if ($rol === 'CICLISTA'): ?>
<div class="mtb-stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(255, 136, 0, 0.1); color: var(--mtb-primary);">
            <i class="fas fa-handshake"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo count($patrocinadores); ?></h3>
            <p>Aliados Activos</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(0, 123, 255, 0.1); color: #007bff;">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-details">
            <h3>2026</h3>
            <p>Temporada Actual</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(40, 167, 69, 0.1); color: #28a745;">
            <i class="fas fa-bullhorn"></i>
        </div>
        <div class="stat-details">
            <p>¿Quieres ser patrocinador?</p>
            <small>Contacta a soporte</small>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($rol === 'ADMIN'): ?>
<section class="tabla-seccion" style="margin-top: 20px;">
    <div class="tabla-card">
        <div class="tabla-header">
            <h3 style="margin:0; font-size: 1.1rem;">
                <i class="fas fa-handshake" style="margin-right:10px;"></i> Gestión de Patrocinadores
            </h3>
        </div>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Empresa / Patrocinador</th>
                        <th>Contacto Principal</th>
                        <th>Estatus</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patrocinadores_lista as $p): ?>
                    <tr>
                        <td style="font-weight: bold; color: #ff6b00;">#<?php echo $p['numeroPatrocinador']; ?></td>
                        <td>
                            <img src="assets/img/patrocinadores/<?php echo $p['logo_patrocinador']; ?>" 
                                 style="height: 40px; width: 60px; object-fit: contain; background: #f9f9f9; border-radius: 4px; padding: 2px;">
                        </td>
                        <td style="font-weight: 600;"><?php echo htmlspecialchars($p['nombrePatrocinador']); ?></td>
                        
                        <td><?php echo htmlspecialchars($p['contactoPatrocinador']); ?></td>
                                <td>
                                    <span class="<?php echo (strtoupper($p['estatus']) == 'ACTIVO') ? 'estatus-activo' : 'estatus-inactivo'; ?>">
                                        <i class="fas fa-circle" style="font-size: 0.6rem; margin-right: 5px;"></i>
                                        <?php echo $p['estatus']; ?>
                                    </span>
                                </td>
                   <td style="text-align: center; display: flex; gap: 8px; justify-content: center;">
                            <a href="#" class="btn-accion btn-actualizar" style="border: 1px solid #ff6b00;" 
   onclick='abrirModalEditarPatrocinador(<?php echo json_encode($p); ?>)'>
    <i class="fas fa-edit"></i>
</a>

                            <?php if(strtoupper($p['estatus']) == 'ACTIVO'): ?>
        <a href="#" class="btn-accion btn-desactivar" style="border: 1px solid #666;" 
           title="Suspender patrocinio"
           onclick="cambiarEstatusPatrocinador(<?php echo $p['numeroPatrocinador']; ?>, 'INACTIVO')">
            <i class="fas fa-ban"></i>
        </a>
    <?php else: ?>
        <a href="#" class="btn-accion btn-reactivar" style="border: 1px solid #28a745;" 
           title="Reactivar patrocinio"
           onclick="cambiarEstatusPatrocinador(<?php echo $p['numeroPatrocinador']; ?>, 'ACTIVO')">
            <i class="fas fa-check-circle"></i>
        </a>
    <?php endif; ?>

                            <a href="#" class="btn-accion btn-delete" 
                               onclick="eliminarPatrocinador(<?php echo $p['numeroPatrocinador']; ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php endif; ?>

<div class="slider-patrocinadores">
    <div class="slider-track">
        <?php 
        // Duplicamos el array para que el efecto infinito no tenga saltos
        $doble_patrocinadores = array_merge($patrocinadores, $patrocinadores);
        foreach ($doble_patrocinadores as $p): 
        ?>
            <div class="slide">
                <img src="assets/img/patrocinadores/<?php echo $p['logo_patrocinador']; ?>" 
                     alt="<?php echo $p['nombrePatrocinador']; ?>"
                     title="<?php echo $p['nombrePatrocinador']; ?>">
            </div>
        <?php endforeach; ?>
    </div>
</div>

        </main>
    </div>
    <!-- FIN CONTENT -->

</div>
<!-- FIN APP -->


<!-- ══════════════════════════════════════════════════════════
     MODAL: NUEVO PATROCINADOR
══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalPatrocinador">
    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-handshake"></i> Nuevo Patrocinador</h2>
            <button class="modal-close" onclick="cerrarModal('modalPatrocinador')">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formPatrocinador" onsubmit="event.preventDefault(); guardarPatrocinador();" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label required">Nombre de la Empresa</label>
                    <div class="input-group">
                        <span class="input-group-icon"><i class="fas fa-building"></i></span>
                        <input type="text" class="form-control" name="nombrePatrocinador" placeholder="Ej. Trek Bikes" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Contacto (Teléfono/Email)</label>
                    <div class="input-group">
                        <span class="input-group-icon"><i class="fas fa-address-book"></i></span>
                        <input type="text" class="form-control" name="contactoPatrocinador" placeholder="Ej. contacto@marca.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">Logo del Patrocinador</label>
                    <input type="file" class="form-control" name="logo_archivo" accept="image/*" required>
                    <small style="color:var(--mtb-gray-500)">Formatos permitidos: JPG, PNG. Máx 2MB.</small>
                </div>

                <div class="modal-footer" style="padding: 20px 0 0 0;">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalPatrocinador')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Patrocinador
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL: EDITAR PATROCINADOR
══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalEditarPatrocinador">
    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-handshake"></i> Modificar Patrocinador</h2>
            <button class="modal-close" onclick="cerrarModal('modalEditarPatrocinador')">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formEditarPatrocinador" method="POST" action="actions/actualizar_patrocinador.php" enctype="multipart/form-data">
<input type="hidden" name="numeroPatrocinador" id="edit_id_patrocinador">                
                <div class="form-group">
        <label class="form-label required">Nombre de la Empresa</label>
        <input type="text" name="nombrePatrocinador" id="edit_nombre_patrocinador" class="form-control" required>
    </div>

    <div class="form-group">
        <label class="form-label">Contacto (Teléfono)</label>
        <input type="text" 
               name="contactoPatrocinador" 
               id="edit_contacto_patrocinador"
               class="form-control" 
               pattern="[0-9]{8,15}" 
               oninput="this.value = this.value.replace(/[^0-9]/g, '');"> 
    </div>

    <div class="form-group">
        <label class="form-label">Actualizar Logo</label>
        <input type="file" name="logo" class="form-control" accept="image/*">
    </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarPatrocinador')">Cancelar</button>
            <button type="submit" form="formEditarPatrocinador" class="btn btn-primary" style="background: #ff6b00; border: none;">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </div>
</div>

<!-- ── TOAST CONTAINER ── -->
<div class="toast-container" id="toastContainer"></div>
<!-- ══════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>


/* ────────────────────────────────────────────────────────────
   MODALES
──────────────────────────────────────────────────────────── */
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

function abrirModalPatrocinador() {
     abrirModal('modalPatrocinador'); 
}
function abrirModalEditarPatrocinador(p) {
    // Asegúrate de usar los nombres EXACTOS de la base de datos
    document.getElementById('edit_id_patrocinador').value = p.numeroPatrocinador;
    document.getElementById('edit_nombre_patrocinador').value = p.nombrePatrocinador;
    document.getElementById('edit_contacto_patrocinador').value = p.contactoPatrocinador;

    abrirModal('modalEditarPatrocinador');
}
function abrirModalEvento()      { abrirModal('modalEvento'); }

// Cerrar modal al hacer clic en el overlay
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) cerrarModal(this.id);
    });
});


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


/* ────────────────────────────────────────────────────────────
   TOAST
──────────────────────────────────────────────────────────── */
function showToast(msg, tipo = 'primary') {
    const iconos = { success: 'fa-circle-check', danger: 'fa-circle-xmark', warning: 'fa-triangle-exclamation', info: 'fa-circle-info', primary: 'fa-bell' };
    const colores = { success: 'var(--mtb-success)', danger: 'var(--mtb-danger)', warning: 'var(--mtb-warning)', info: 'var(--mtb-info)', primary: 'var(--mtb-primary)' };

    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.borderLeftColor = colores[tipo] || colores.primary;
    toast.innerHTML = `<i class="fas ${iconos[tipo] || iconos.primary}" style="color:${colores[tipo]};"></i><span>${msg}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(30px)';
        toast.style.transition = 'all .3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}


</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg === 'ok') {
        Swal.fire({
            title: '¡Bienvenido a MTB nogales!',
            text: 'se ha iniciado sesion correctamente.',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'ok2'){
        Sawl.fire({
            title: '¡Cuenta creada exitosamente!',
            text: 'Bienvenido a MTB nogales, ahora puedes iniciar sesión con tus credenciales.',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'ok3'){
        
        Swal.fire({
       
            title: '¡Sesión cerrada!',
            text: 'Has salido de tu cuenta de forma segura. ¡Vuelve pronto!',
            icon: 'info',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'bienvenido_de_nuevo_ok')
    {
        Swal.fire({
            title: '¡Bienvenido de nuevo a MTB nogales!',
            text: 'Has reactivado tu cuenta nuevamente. ¡Esperamos y disfrutes tu estadia!',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'actualizado_ok')
    {
        Swal.fire({
            title: '¡Patrocinador actualizado!',
            text: 'Los cambios se han guardado correctamente.',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    
    
 function limpiarURL() {
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
    }
});

function confirmarCierreSesion(event) {
    event.preventDefault(); 
    Swal.fire({
        title: '¿Seguro que quieres cerrar sesión?',
        text: "Tendrás que volver a ingresar tus credenciales.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E8630A',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'actions/cerrarSesion.php'; 
        }
    });
}
</script>
<script>
    // Función para ELIMINAR definitivamente
function eliminarPatrocinador(id) {
    Swal.fire({
        title: '¿Eliminar patrocinador?',
        text: "Esta acción no se puede deshacer y se borrará de la base de datos.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#666',
        confirmButtonText: 'Sí, eliminar permanentemente'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('actions/eliminar_patrocinador.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Eliminado', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}

// Función para REACTIVAR o DAR DE BAJA (Usa la que ya te di, pero asegúrate de que sea así)
function cambiarEstatusPatrocinador(id, nuevoEstatus) {
    const accion = nuevoEstatus === 'Inactivo' ? 'dar de baja' : 'reactivar';
    
    Swal.fire({
        title: `¿Estás seguro?`,
        text: `Vas a ${accion} este patrocinador.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff6b00',
        cancelButtonColor: '#666',
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviamos los datos al servidor
            fetch('actions/cambiar_estatus_patrocinador.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&estatus=${nuevoEstatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('¡Listo!', data.message, 'success').then(() => {
                        location.reload(); // Recargamos para ver el cambio de icono
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}
function checarEstatusVivo() {
    fetch('verificar_estatus.php')
        .then(response => response.json())
        .then(data => {
            if (data.activo === false) {
                window.location.href = 'index.php?error=Tu cuenta ha sido desactivada.';
            }
        })
        .catch(error => console.error('Error verificando sesión:', error));
}

// Ejecutar cada 5 segundos (5000 milisegundos)
setInterval(checarEstatusVivo, 5000);

function guardarPatrocinador() {
    const form = document.getElementById('formPatrocinador');
    const formData = new FormData(form);

    fetch('actions/guardar_patrocinador.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Verificamos si la respuesta es un JSON válido antes de continuar
        return response.json();
    })
    .then(data => {
        // Cerramos el modal antes de mostrar la alerta
        cerrarModal('modalPatrocinador');

        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Registrado!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload(); 
            });
        } else {
            // Aquí mostrará los errores de duplicado o formato
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        cerrarModal('modalPatrocinador');
        Swal.fire({
            icon: 'error',
            title: 'Error de sistema',
            text: 'La respuesta del servidor no fue válida. Revisa la consola (F12).'
        });
    });
}
</script>
</body>
</html>
