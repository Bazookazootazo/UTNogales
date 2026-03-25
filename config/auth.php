<?php
// Evitamos doble sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/conexion.php'; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: registro.php");
    exit();
}

$id_logueado = $_SESSION['id_usuario'];

try {
    // Agregados: correoUser, telefonoUser, estatus (y usamos numeroUser como en tu viejo cuenta.php)
    $query_user = "SELECT nombreUser, apellidosUser, correoUser, telefonoUser, rol, estatus FROM usuarios WHERE numeroUser = ?";
    $stmt = $conn->prepare($query_user);
    $stmt->execute([$id_logueado]);
    $datos_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($datos_usuario) {
        // Asignamos todas las variables para que estén disponibles en cualquier página
        $nombre_completo = $datos_usuario['nombreUser'] . " " . $datos_usuario['apellidosUser'];
        $correo = $datos_usuario['correoUser'];
        $telefono = $datos_usuario['telefonoUser'];
        $rol = $datos_usuario['rol'];
        $estatus = $datos_usuario['estatus'];
        
        $n = mb_substr($datos_usuario['nombreUser'], 0, 1);
        $a = mb_substr($datos_usuario['apellidosUser'], 0, 1);
        $iniciales = strtoupper($n . $a);
    } else {
        session_destroy();
        header("Location: registro.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>