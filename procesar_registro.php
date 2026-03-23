<?php
header('Content-Type: application/json');
require 'conexion.php'; 

try {
    // 1. Encriptar
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $equipo = !empty($_POST['equipo']) ? (int)$_POST['equipo'] : 0;

    // 2. Llamar al SP (Asegúrate de que sean 7 signos de interrogación)
    $stmt = $conn->prepare("CALL sp_registroUsuario(?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['correo'], 
        $hash, 
        $_POST['nombre'], 
        $_POST['apellidos'], 
        $_POST['telefono'], 
        $_POST['fecha_nacimiento'], 
        $equipo
    ]);

    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($res);

} catch (PDOException $e) {
    // Esto nos dirá el error real en la consola de F12
    echo json_encode(['estado' => 'ERROR', 'mensaje' => $e->getMessage()]);
}
?>