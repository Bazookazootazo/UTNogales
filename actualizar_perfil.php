<?php
session_start();
include 'config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id_usuario'])) {
    
    $id = !empty($_POST['id_usuario_a_editar']) ? $_POST['id_usuario_a_editar'] : $_SESSION['id_usuario'];
    
    $nombre = trim($_POST['nuevo_nombre']);
    $apellido = trim($_POST['nuevo_apellido']);
    $telefono = trim($_POST['nuevo_telefono']);
    $correo = trim($_POST['nuevo_correo']);

    try {
        $stmt = $conn->prepare("CALL sp_actualizarPerfil(?, ?, ?, ?, ?)");
        $stmt->execute([$id, $nombre, $apellido, $telefono, $correo]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

$url_regreso = (!empty($_POST['id_usuario_a_editar'])) ? "administracion_de_usuarios.php" : "cuenta.php";

if ($res && $res['estado'] === 'EXITO') {
    header("Location: $url_regreso?msg=update_ok");
} else {
    $mensajeError = isset($res['mensaje']) ? $res['mensaje'] : 'Error desconocido';
    header("Location: $url_regreso?msj=edit_error&error_text=" . urlencode($mensajeError));
}
// ...
    } catch (PDOException $e) {
        $url_regreso = (!empty($_POST['id_usuario_a_editar'])) ? "administracion_de_usuarios.php" : "cuenta.php";
        header("Location: $url_regreso?msj=edit_error&error_text=Error de servidor");
    }
    exit();
}