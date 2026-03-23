<?php
// procesar_registro.php
header('Content-Type: application/json');
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibir los datos del formulario
    $correo = $_POST['correo'];
    $password_plana = $_POST['password'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    
    // Si no manda equipo, enviamos NULL al SP
    $equipo = !empty($_POST['equipo']) ? $_POST['equipo'] : null;

    // 2. Encriptar la contraseña antes de mandarla a la BD
    $password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

    try {
        // 3. Preparar la llamada al Stored Procedure siguiendo tu regla de negocio
        $stmt = $pdo->prepare("CALL sp_registroUsuario(?, ?, ?, ?, ?, ?, ?)");
        
        // 4. Ejecutar pasando los parámetros en el orden exacto del SP
        $stmt->execute([
            $correo, 
            $password_hash, 
            $nombre, 
            $apellidos, 
            $telefono, 
            $fecha_nacimiento, 
            $equipo
        ]);

        // 5. Capturar la respuesta que devuelve el SP (el SELECT final)
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Devolver la respuesta del SP al frontend (JavaScript)
        echo json_encode($resultado);

    } catch (PDOException $e) {
        // Si falla por llave duplicada (ej. correo ya existe) o error de SQL
        echo json_encode([
            'estado' => 'ERROR', 
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ]);
    }
}
?>