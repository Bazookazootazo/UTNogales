<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ciclista</title>
    <style>
        /* CSS Vanilla: Limpio, centrado y funcional */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .formulario-contenedor {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .formulario-contenedor h2 {
            margin-top: 0;
            color: #333;
            text-align: center;
        }
        .grupo-input {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
            font-size: 14px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
        #mensaje {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            display: none;
            text-align: center;
        }

        /* Errores debajo de los inputs */
        .error-input {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        /* El cuadro de mensaje general (éxito/error) */
        #mensaje {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            display: none;
            text-align: center;
        }

        .exito { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error-msg { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="formulario-contenedor">
    <h2>Nuevo Ciclista</h2>
    <form id="formRegistro">
        <div class="grupo-input">
            <label for="correo">Correo Electrónico *</label>
            <input type="email" id="correo" name="correo" required>
        </div>
        <div class="grupo-input">
            <label for="password">Contraseña *</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="grupo-input">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>
        <div class="grupo-input">
            <label for="apellidos">Apellidos *</label>
            <input type="text" id="apellidos" name="apellidos" required>
        </div>
        <div class="grupo-input">
            <label for="telefono">Teléfono *</label>
            <input type="tel" id="telefono" name="telefono" required>
        </div>
        <div class="grupo-input">
            <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
        </div>
        <div class="grupo-input">
            <label for="equipo">ID de Equipo (Opcional)</label>
            <input type="number" id="equipo" name="equipo" placeholder="Dejar en blanco si no tiene">
        </div>
        
        <button type="submit">Registrar</button>
    </form>
    
    <div id="mensaje"></div>
</div>

<script>
document.getElementById('formRegistro').addEventListener('submit', function(e) {
    e.preventDefault(); // Detenemos el envío para validar primero

    let isValid = true;
    const divMensajeGeneral = document.getElementById('mensaje');
    
    // Captura de valores (Asegúrate de que los IDs coincidan con tu HTML)
    const correo = document.getElementById('correo').value.trim();
    const password = document.getElementById('password').value;
    const nombre = document.getElementById('nombre').value.trim();
    const apellidos = document.getElementById('apellidos').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const fecha = document.getElementById('fecha_nacimiento').value;

    // 1. Limpiar errores visuales anteriores
    document.querySelectorAll('.error-input').forEach(el => el.remove());
    divMensajeGeneral.style.display = 'none';

    // 2. TUS VALIDACIONES (Lógica adaptada)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(correo)) {
        mostrarError('correo', 'Correo electrónico no válido.');
        isValid = false;
    }

    if (password.length < 6) {
        mostrarError('password', 'La contraseña debe tener al menos 6 caracteres.');
        isValid = false;
    }

    const nombreRegex = /^[a-zA-ZáéíóúñÑ\s]+$/;
    if (!nombreRegex.test(nombre)) {
        mostrarError('nombre', 'El nombre solo puede contener letras y espacios.');
        isValid = false;
    }
    if (!nombreRegex.test(apellidos)) {
        mostrarError('apellidos', 'Los apellidos solo pueden contener letras y espacios.');
        isValid = false;
    }

    const telefonoRegex = /^\d{8,15}$/;
    if (!telefonoRegex.test(telefono)) {
        mostrarError('telefono', 'El teléfono debe tener entre 8 y 15 dígitos.');
        isValid = false;
    }

    if (fecha) {
        const hoy = new Date();
        const fechaNac = new Date(fecha);
        if (fechaNac >= hoy) {
            mostrarError('fecha_nacimiento', 'La fecha debe ser anterior a hoy.');
            isValid = false;
        }
    }

    // 3. SI TODO ES VÁLIDO, EJECUTAMOS EL FETCH (Llamada al SP vía PHP)
    if (isValid) {
        const formData = new FormData(this);

        fetch('procesar_registro.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            divMensajeGeneral.style.display = 'block';
            if(data.estado === 'EXITO') {
                divMensajeGeneral.className = 'exito';
                divMensajeGeneral.innerText = data.mensaje;
                document.getElementById('formRegistro').reset();
            } else {
                divMensajeGeneral.className = 'error-msg';
                divMensajeGeneral.innerText = data.mensaje;
            }
        })
        .catch(error => {
            divMensajeGeneral.style.display = 'block';
            divMensajeGeneral.className = 'error-msg';
            divMensajeGeneral.innerText = 'Error crítico en el servidor.';
        });
    }
});

// Tu función para mostrar errores debajo de los campos
function mostrarError(campoId, mensaje) {
    const campo = document.getElementById(campoId);
    const errorDiv = document.createElement('span');
    errorDiv.className = 'error-input';
    errorDiv.textContent = mensaje;
    campo.parentNode.insertBefore(errorDiv, campo.nextSibling);
}
</script>

</body>
</html>