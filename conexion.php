<?php
$host = "localhost";    // Cambiado de servername a host
$dbname = "mtbnog";     // Cambiado de database a dbname
$username = "root";
$password = "";

try {
    // La cadena corregida: mysql:host=...;dbname=...
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Opcional: Desactivar emulación de preparados para mayor seguridad con SPs
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // Es buena práctica mandar un código de respuesta HTTP 500 si falla la conexión
    http_response_code(500);
    die(json_encode(['estado' => 'ERROR', 'mensaje' => 'Error de conexión: ' . $e->getMessage()]));
}
?>