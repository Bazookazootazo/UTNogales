<?php 
$pagina_actual = 'AdministracionUsuarios';
session_start();
include 'config/conexion.php'; 

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'ADMIN') {
    header("Location: inicio.php"); 
    exit();
}

$id_logueado = $_SESSION['id_usuario'];

try {
    $query_admin = "SELECT nombreUser, apellidosUser, telefonoUser, rol, estatus FROM usuarios WHERE numeroUser = ?";
    $stmt = $conn->prepare($query_admin);
    $stmt->execute([$id_logueado]);
    $datos_admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $nombre_completo = $datos_admin['nombreUser'] . " " . $datos_admin['apellidosUser'];
    $rol = $datos_admin['rol'];
    $iniciales = strtoupper(mb_substr($datos_admin['nombreUser'], 0, 1) . mb_substr($datos_admin['apellidosUser'], 0, 1));

    $query_lista = "SELECT numeroUser, nombreUser, apellidosUser, correoUser, telefonoUser, rol, estatus FROM usuarios";
    $stmt_lista = $conn->prepare($query_lista);
    $stmt_lista->execute();
    $usuarios = $stmt_lista->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
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
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>MTB Sistema — Administración de Usuarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/mtb-dashboard.css" />
        <?php include_once 'includes/header_sidebar.php'; ?>

    <style>
        .mtb-app { display: flex; width: 100%; min-height: 100vh; }
        .mtb-content { flex: 1; display: flex; flex-direction: column; background-color: #f4f7f6; }
        
        /* Estilos de la Tabla */
        .tabla-seccion { padding: 30px; }
        .tabla-card { background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #eee; }
        .tabla-header { background: #2f3430; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 4px solid #ff6b00; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #fafafa; color: #888; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #f1f1f1; color: #444; font-size: 0.9rem; }
        tr:hover { background-color: #fcfcfc; }

        .badge-rol { background: #2f3430; color: white; padding: 4px 10px; border-radius: 50px; font-size: 0.7rem; font-weight: bold; }
        .estatus-activo { color: #28a745; font-weight: bold; }
        .estatus-inactivo { color: #dc3545; font-weight: bold; }
        
        .btn-accion { padding: 8px; border-radius: 6px; transition: 0.3s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-delete { color: #dc3545; border: 1px solid #dc3545; background: transparent; }
        .btn-delete:hover { background: #dc3545; color: white; }
        .btn-desactivar { color: #666; border: 1px solid ##666; background: transparent; }
        .btn-desactivar:hover { background: #666; color: white; }
        .btn-actualizar { color: #ff6b00; border: 1px solid #ff6b00; background: transparent; }
        .btn-actualizar:hover { background: #e8630a; color: white;}
        .btn-reactivar { color: #28a745; border: 1px solid #28a745; background: transparent; }
        .btn-reactivar:hover { background: #218838; color: white; }
    </style>
</head>
<body>

<div class="mtb-app">

   

    <main class="mtb-content">
        <header class="mtb-topbar" style="background: #fff; padding: 15px 30px; border-bottom: 1px solid #e0e0e0;">
            <div class="topbar-title">Administración de Usuarios</div>
            <div style="font-size: 0.85rem; color: #888;">Gestión de personal y accesos</div>
        </header>

        <section class="tabla-seccion">
            <div class="tabla-card">
                <div class="tabla-header">
                    <h3 style="margin:0; font-size: 1.1rem;"><i class="fas fa-list" style="margin-right:10px;"></i> Usuarios Registrados</h3>
                </div>
                
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Correo Electrónico</th>
                                <th>Numero de teléfono</th>
                                <th>Rol</th>
                                <th>Estatus</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $row): ?>
                            <tr>
                                <td style="font-weight: bold; color: #ff6b00;">#<?php echo $row['numeroUser']; ?></td>
                                <td><?php echo htmlspecialchars($row['nombreUser'] . " " . $row['apellidosUser']); ?></td>
                                <td><?php echo htmlspecialchars($row['correoUser']); ?></td>
                                <td><?php echo htmlspecialchars($row['telefonoUser']); ?></td>
                                <td><span class="badge-rol"><?php echo $row['rol']; ?></span></td>
                                <td>
                                    <span class="<?php echo ($row['estatus'] == 'Activo') ? 'estatus-activo' : 'estatus-inactivo'; ?>">
                                        <i class="fas fa-circle" style="font-size: 0.6rem; margin-right: 5px;"></i>
                                        <?php echo $row['estatus']; ?>
                                    </span>
                                </td>
                                <td style="text-align: center; display: flex; gap: 8px; justify-content: center;">
    <?php if($row['numeroUser'] != $id_logueado): ?>
        <a href="#" class="btn-accion btn-actualizar" style="border: 1px solid #ff6b00;" 
           onclick="abrirModalEditarAdmin(<?php echo htmlspecialchars(json_encode($row)); ?>)">
            <i class="fas fa-user-edit"></i>
        </a>

        <?php if($row['estatus'] == 'Activo'): ?>
            <a href="#" class="btn-accion btn-desactivar" style=" border: 1px solid #666;" 
               title="Dar de baja"
               onclick="confirmarBaja(<?php echo $row['numeroUser']; ?>, '<?php echo $row['nombreUser']; ?>')">
                <i class="fas fa-user-clock"></i>
            </a>
        <?php else: ?>
            <a href="#" class="btn-accion btn-reactivar" style="border: 1px solid #28a745;" 
               title="Reactivar cuenta"
               onclick="confirmarAlta(<?php echo $row['numeroUser']; ?>, '<?php echo $row['nombreUser']; ?>')">
                <i class="fas fa-user-check"></i>
            </a>
        <?php endif; ?>

        <a href="#" class="btn-accion btn-delete" onclick="eliminarUsuario(<?php echo $row['numeroUser']; ?>)">
            <i class="fas fa-trash-alt"></i>
        </a>
    <?php else: ?>
        <span style="font-size: 0.8rem; color: #ccc;">(Tú)</span>
    <?php endif; ?>
</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>


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
            <form id="formEditarPerfil" method="POST" action="actualizar_perfil.php">
    <input type="hidden" name="id_usuario_a_editar" id="edit_id_user">
                <div class="form-group">
                    <label class="form-label required">Nombres</label>
                    <input type="text" name="nuevo_nombre" class="form-control" value="<?php echo $datos_usuario['nombreUser']; ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Apellidos</label>
                        <input type="text" name="nuevo_apellido" class="form-control" value="<?php echo $datos_usuario['apellidosUser']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="nuevo_correo" class="form-control" value="<?php echo $correo; ?>" required>
                    </div>
                </div>
                <div class="form-group">
    <label class="form-label">Número de teléfono</label>
    <input type="text" 
           name="nuevo_telefono" 
           class="form-control" 
           value="<?php echo $telefono; ?>" 
           placeholder="Ej. 631 7262 232"
           pattern="[0-9]{8,15}" 
           title="El teléfono debe tener entre 8 y 15 números"
           oninput="this.value = this.value.replace(/[^0-9]/g, '');"> 
           </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalEditarUsuarios')">Cancelar</button>
            <button type="submit" form="formEditarPerfil" class="btn btn-primary" style="background: #ff6b00; border: none;">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function eliminarUsuario(id) {
        Swal.fire({
            title: '¿Borrar del sistema?',
            text: "El usuario ya no podrá acceder al sistema.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff6b00',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, borrar permanentemente',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `borrarUsuario.php?numeroUser=${id}`;
            }
        });
    }

    // Sidebar logic
    const toggleBtn = document.getElementById('toggleSidebar');
    if(toggleBtn){
        toggleBtn.addEventListener('click', () => {
            document.getElementById('mtbSidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        });
    }

function abrirModal(id) {
    document.getElementById(id).classList.add('active');
}

function cerrarModal(id) {
    document.getElementById(id).classList.remove('active');
}

function abrirModalEditarAdmin(usuario) {
    // Asegúrate de que los nombres coincidan con los IDs de los inputs
    document.getElementsByName('nuevo_nombre')[0].value = usuario.nombreUser;
    document.getElementsByName('nuevo_apellido')[0].value = usuario.apellidosUser;
    document.getElementsByName('nuevo_correo')[0].value = usuario.correoUser;
    document.getElementsByName('nuevo_telefono')[0].value = usuario.telefonoUser;
    
    document.getElementById('edit_id_user').value = usuario.numeroUser;
    
    abrirModal('modalEditarUsuarios'); 
}
// FUNCIÓN PARA DAR DE BAJA (Corregida la ruta a actions/)
function confirmarBaja(id, nombre) {
    Swal.fire({
        title: '¿Dar de baja del sistema?',
        text: `El usuario ${nombre} no podrá iniciar sesión hasta que sea reactivado.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#666',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Agregamos "actions/" para que lo encuentre
            window.location.href = `actions/dar_de_baja_cuenta.php?numeroUser=${id}&nuevo_estatus=Inactivo`;
        }
    });
}

// FUNCIÓN PARA REACTIVAR (Corregida para que coincida con la de arriba)
function confirmarAlta(id, nombre) {
    Swal.fire({
        title: '¿Reactivar cuenta?',
        text: `El usuario ${nombre} podrá volver a acceder al sistema.`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#666',
        confirmButtonText: 'Sí, reactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aseguramos que la ruta sea la misma
            window.location.href = `actions/dar_de_baja_cuenta.php?numeroUser=${id}&nuevo_estatus=Activo`;
        }
    });
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg === 'borrado_ok') {
        Swal.fire({
            title: '¡EL usuario ha sido borrado exitosamente!',
            text: 'El usuario ha sido eliminado del sistema.',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'baja_ok') {
        Swal.fire({
            title: '¡Usuario desactivado!',
            text: 'El usuario ha sido dado de baja del sistema. Puede ser reactivado posteriormente a 30 dias.',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'update_ok') {
        Swal.fire({
            title: '¡Perfil actualizado!',
            text: 'Los cambios en el perfil se han guardado correctamente.',
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

// Manejo de alertas según la respuesta del servidor
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const mensaje = urlParams.get('msj') || urlParams.get('msg');
    const errorTexto = urlParams.get('error_text');

    if (mensaje) {
        let config = {
            confirmButtonColor: '#ff6b00',
            timer: 3000
        };

        switch (mensaje) {
    case 'edit_error':
    config.title = 'No se pudo actualizar';
    config.text = errorTexto || 'Ocurrió un error inesperado en el sistema.'; 
    config.icon = 'error';
    config.timer = null; 
    break;
        }

        if (config.title) {
            Swal.fire(config).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    }
});
</script>
<script>
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
</script>
</body>
</html>

    