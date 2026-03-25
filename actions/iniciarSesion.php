<?php
session_start();
require '../config/conexion.php';

if (isset($_POST['submit'])) {
    $correo = $_POST['nombre'] ?? '';
    $pass_input = $_POST['contraseña'] ?? '';

    try {
        // 1. Buscamos el hash de la contraseña y el ID para la validación inicial
        $stmt = $conn->prepare("SELECT numeroUser, contraseñaUser FROM usuarios WHERE correoUser = ?");
        $stmt->execute([$correo]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 2. Verificamos si la contraseña coincide con el hash de la BD
        $es_valida = ($user_data && password_verify($pass_input, $user_data['contraseñaUser']));

        // 3. Llamamos al Procedimiento Almacenado de login
        // Enviamos el correo y 1 si la clave es correcta, o 0 si no lo es
        $stmt = $conn->prepare("CALL sp_login(?, ?)");
        $stmt->execute([$correo, $es_valida ? 1 : 0]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 

        if ($res) {
            // Normalizamos la respuesta del SP a mayúsculas para evitar errores de escritura
            $estado_sp = strtoupper($res['estado'] ?? '');

            // CASO A: El usuario tiene la contraseña bien pero su cuenta está INACTIVA
            if ($estado_sp === 'INACTIVO') {
                $_SESSION['id_reactivar'] = $res['id']; // Guardamos el ID para la página de reactivación
                header("Location: ../reactivacion.php");
                exit();
            }

            // CASO B: Todo correcto, acceso concedido
            if ($estado_sp === 'EXITO') {
                $_SESSION['id_usuario'] = $res['id'];
                $_SESSION['rol'] = $res['rol'];
                header("Location: ../inicio.php?msg=ok");
                exit();
            }

            // CASO C: Error (Correo no existe o contraseña mal) enviado por el SP
            header("Location: ../index.php?error=" . urlencode($res['mensaje'] ?? 'Credenciales incorrectas'));
            exit();
        }

    } catch (PDOException $e) {
        // Error de conexión o de base de datos
        header("Location: ../index.php?error=Error técnico en el servidor.");
        exit();
    }
} else {
    // Si intentan entrar al archivo sin enviar el formulario
    header("Location: ../index.php");
    exit();
}
?>