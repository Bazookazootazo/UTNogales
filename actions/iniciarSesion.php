<?php
session_start();
require '../config/conexion.php';

if (isset($_POST['submit'])) {
    $correo = $_POST['nombre'] ?? '';
    $pass_input = $_POST['contraseña'] ?? '';

    try {
        $stmt = $conn->prepare("SELECT contraseñaUser FROM usuarios WHERE correoUser = ?");
        $stmt->execute([$correo]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $es_valida = ($user_data && password_verify($pass_input, $user_data['contraseñaUser']));

        $stmt = $conn->prepare("CALL sp_login(?, ?)");
        $stmt->execute([$correo, $es_valida ? 1 : 0]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 

        if ($res && $res['estado'] === 'EXITO') {
            $_SESSION['id_usuario'] = $res['id'];
            $_SESSION['rol'] = $res['rol'];
            header("Location: ../inicio.php?msg=ok");
            exit();
        } else {
            header("Location: ../index.php?error=" . urlencode($res['mensaje'] ?? 'Error desconocido'));
            exit();
        }
        
    } catch (PDOException $e) {
        header("Location: ../index.php?error=Error de sistema.");
        exit();
    }
}
?>