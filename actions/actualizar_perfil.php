<?php
session_start();
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id_usuario'])) {
$id = (!empty($_POST['id_usuario_a_editar'])) ? $_POST['id_usuario_a_editar'] : $_SESSION['id_usuario'];    $nombre = trim($_POST['nuevo_nombre']);
    $apellido = trim($_POST['nuevo_apellido']);
    $telefono = trim($_POST['nuevo_telefono']);
    $correo = trim($_POST['nuevo_correo']);

    try {
    $stmt = $conn->prepare("CALL sp_actualizarPerfil(?, ?, ?, ?, ?)");
    $stmt->execute([$id, $nombre, $apellido, $telefono, $correo]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

if ($res && $res['estado'] === 'EXITO') {
    // Si el ID editado es distinto al de mi sesión, vengo de administración
    if (!empty($_POST['id_usuario_a_editar']) && $_POST['id_usuario_a_editar'] != $_SESSION['id_usuario']) {
        header("Location: ../administracion_de_usuarios.php?msg=update_ok");
    } else {
        header("Location: ../cuenta.php?msj=edit_ok");
    }
} else {
    // Lo mismo para el error
    $destino = (!empty($_POST['id_usuario_a_editar']) && $_POST['id_usuario_a_editar'] != $_SESSION['id_usuario']) 
               ? "../administracion_de_usuarios.php" 
               : "../cuenta.php";
    header("Location: " . $destino . "?msg=edit_error&error_text=" . urlencode($res['mensaje']));
}
} catch (PDOException $e) {
    header("Location: ../cuenta.php?msj=edit_error&error_text=Error de servidor");
}
    exit();
}