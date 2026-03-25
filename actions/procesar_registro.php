<?php
session_start();
header('Content-Type: application/json');
require '../config/conexion.php'; 

try {
    $pass_original = $_POST['password']; 
    $hash = password_hash($pass_original, PASSWORD_DEFAULT); 
    $equipo = !empty($_POST['equipo']) ? (int)$_POST['equipo'] : 0;

    $stmt = $conn->prepare("CALL sp_registroUsuario(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['correo'], 
        $hash, 
        $pass_original,
        $_POST['nombre'], 
        $_POST['apellidos'], 
        $_POST['telefono'], 
        $_POST['fecha_nacimiento'], 
        $equipo
    ]);

    // Obtenemos el resultado del SELECT que hace el SP
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // IMPORTANTE: Cerramos el cursor para que no dé error de red en la siguiente petición
    $stmt->closeCursor();

    if ($res && $res['estado'] === 'EXITO') {
        $_SESSION['id_usuario'] = $res['id_generado']; 
        $_SESSION['rol'] = 'CICLISTA'; 
    }

    echo json_encode($res);

} catch (PDOException $e) {
    echo json_encode(['estado' => 'ERROR', 'mensaje' => 'Error de SQL: ' . $e->getMessage()]);
}
?>