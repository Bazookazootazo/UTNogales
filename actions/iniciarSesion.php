<?php
session_start();
require '../config/conexion.php';

if (isset($_POST['submit'])) {
    $correo = $_POST['nombre'] ?? '';
    $pass_input = $_POST['contraseña'] ?? '';

    try {
        // 1. Obtenemos solo la contraseña y el ID para la verificación inicial
        $stmt = $conn->prepare("SELECT numeroUser, contraseñaUser FROM usuarios WHERE correoUser = ?");
        $stmt->execute([$correo]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 2. Verificamos si la contraseña es válida
        $es_valida = ($user_data && password_verify($pass_input, $user_data['contraseñaUser']));

        // 3. Llamamos al SP de login enviando el resultado de la validación
        $stmt = $conn->prepare("CALL sp_login(?, ?)");
        $stmt->execute([$correo, $es_valida ? 1 : 0]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 

<<<<<<< HEAD:iniciarSesion.php
        if ($res) {
            // --- CAMBIO CLAVE AQUÍ ---
            // Usamos 'estado' en lugar de 'estatus' y comparamos con 'INACTIVO' en mayúsculas
            $estado_sp = strtoupper($res['estado'] ?? '');

            // CASO A: El SP detecta que está INACTIVO
            if ($estado_sp === 'INACTIVO') {
                $_SESSION['id_reactivar'] = $res['id']; 
                header("Location: reactivacion.php");
                exit(); // Cortamos la ejecución aquí
            }

            // CASO B: El SP da luz verde (EXITO)
            if ($estado_sp === 'EXITO') {
                $_SESSION['id_usuario'] = $res['id'];
                $_SESSION['rol'] = $res['rol'];
                header("Location: inicio.php?msg=ok");
                exit();
            }

            // CASO C: El SP detecta error (Contraseña mal, usuario no existe, etc.)
            header("Location: index.php?error=" . urlencode($res['mensaje'] ?? 'Error de acceso'));
=======
        if ($res && $res['estado'] === 'EXITO') {
            $_SESSION['id_usuario'] = $res['id'];
            $_SESSION['rol'] = $res['rol'];
            header("Location: ../inicio.php?msg=ok");
            exit();
        } else {
            header("Location: ../index.php?error=" . urlencode($res['mensaje'] ?? 'Error desconocido'));
>>>>>>> 6570f7fed8af42ae14b5d289d778075e852666da:actions/iniciarSesion.php
            exit();
        }
        
    } catch (PDOException $e) {
        header("Location: ../index.php?error=Error de sistema.");
        exit();
    }
}
?>