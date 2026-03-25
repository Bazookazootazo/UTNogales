<?php 
$pagina_actual = 'cuenta';
require_once 'config/auth.php'; 
?>

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
                    <label class="form-label required">Nombres</label>
                    <input type="text" class="form-control" placeholder="Ej. Enrique Manuel" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Apellidos</label>
                        <input type="text" class="form-control" placeholder="Ej. Rodriguez Garcia" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Correo electronico</label>
                        <input type="email" class="form-control" placeholder="Ej. enrique.rodriguez@example.com">
                    </div>
                </div>
                <div class="form-group">
                 <div class="form-group">
                        <label class="form-label">Numero de telefono</label>
                        <input type="text" class="form-control" placeholder="Ej. 631 7262 232">
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