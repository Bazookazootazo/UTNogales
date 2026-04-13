<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ciclista - MTB Nogales</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #E8630A;
            --primary-dark: #863e0a;
            --bg-card: #1A1F2E;
            --font-principal: 'Source Sans 3', sans-serif;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: var(--font-principal);
        }

        /* Fondo con imagen y overlay idéntico al login */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(rgba(122, 158, 50, 0.6), rgba(0, 0, 0, 0.6)), url('assets/img/MTB_BG.jpeg');
            background-size: cover;
            background-position: center;
            padding: 40px 20px;
        }

        /* Tarjeta oscura centrada */
        .login-card {
            background: var(--bg-card);
            width: 100%;
            max-width: 500px; /* Un poco más ancho para el registro */
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-header h2 {
            font-size: 1.6rem;
            color: #ffffff;
            margin: 0;
        }

        .login-header p {
            color: var(--primary);
            font-weight: 600;
            margin-top: 5px;
        }

        /* Estilo de los grupos de input */
        .input-group { margin-bottom: 18px; }

        .input-group label {
            display: block;
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            color: #64748b;
            font-size: 1rem;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #334155;
            border-radius: 10px;
            font-size: 0.95rem;
            background-color: #0f172a;
            color: white;
            transition: all 0.3s;
        }

        .input-wrapper input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(232, 99, 10, 0.2);
            outline: none;
            background-color: #1e293b;
        }

        /* Botón Naranja */
        .btn-register {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-register:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(232, 99, 10, 0.3);
        }

        .login-footer {
            margin-top: 25px;
            text-align: center;
        }

        .login-footer a {
            text-decoration: none;
            color: var(--primary);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .copy {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 15px;
        }

        /* Estilos para inputs de tipo fecha y número */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1); /* Hace el icono del calendario blanco */
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h2>Crear Cuenta</h2>
            <p>Únete a la comunidad MTB Nogales</p>
        </div>

        <form id="formRegistro">
            <div class="input-group">
                <label>Correo Electrónico *</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="correo" placeholder="ejemplo@correo.com" required>
                </div>
            </div>

            <div class="input-group">
                <label>Contraseña *</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-group">
                    <label>Nombre *</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="nombre" placeholder="Tu nombre" required>
                    </div>
                </div>
                <div class="input-group">
                    <label>Apellidos *</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-address-card"></i>
                        <input type="text" name="apellidos" placeholder="Tus apellidos" required>
                    </div>
                </div>
            </div>

            <div class="input-group">
                <label>Teléfono *</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-phone"></i>
                    <input type="tel" name="telefono" placeholder="631-xxx-xxxx" required>
                </div>
            </div>

            <div class="input-group">
                <label>Fecha de Nacimiento *</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-calendar"></i>
                    <input type="date" name="fecha_nacimiento" required>
                </div>
            </div>

            <div class="input-group">
                <label>ID de Equipo (Opcional)</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-users"></i>
                    <input type="number" name="equipo" placeholder="Dejar vacío si no tienes">
                </div>
            </div>
            
            <button type="submit" class="btn-register">Finalizar Registro</button>
        </form>
        
        <div class="login-footer">
            <a href="index.php">¿Ya tienes cuenta? Inicia sesión</a>
            <p class="copy">&copy; <?php echo date("Y"); ?> MTB Nogales Sonora</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('formRegistro').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    fetch('actions/procesar_registro.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.estado === 'EXITO') {
            Swal.fire({
                title: '¡Bienvenido Ciclista!',
                text: data.mensaje,
                icon: 'success',
                confirmButtonColor: '#E8630A',
                background: '#1A1F2E',
                color: '#fff'
            }).then(() => {
                window.location.href = 'inscripciones.php';
            });
            
            setTimeout(() => {
                window.location.href = 'inscripciones.php';
            }, 2000);
            
        } else {
            Swal.fire({
                title: 'Atención',
                text: data.mensaje,
                icon: 'warning',
                confirmButtonColor: '#d33',
                background: '#1A1F2E',
                color: '#fff'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error de Red',
            text: 'No se pudo procesar la solicitud.',
            icon: 'error',
            background: '#1A1F2E',
            color: '#fff'
        });
    });
});
window.addEventListener('DOMContentLoaded', (event) => {
    const urlParams = new URLSearchParams(window.location.search);
    const mensaje = urlParams.get('msj');

    if (mensaje === 'cuenta_desactivada') {
        Swal.fire({
            title: 'Cuenta Desactivada',
            html: 'Tu cuenta ha sido dada de baja correctamente.<br><br>' +
                  '<b>Nota:</b> Tienes un periodo de <strong>30 días</strong> para reactivarla antes de que tus datos sean eliminados permanentemente.',
            icon: 'info',
            confirmButtonColor: '#E8630A',
            background: '#1A1F2E',
            color: '#fff'
        });
        
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>
</body>
</html>