<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id_usuario'])) {
    $id = $_SESSION['id_usuario'];
    $nombre = trim($_POST['nuevo_nombre']);
    $apellido = trim($_POST['nuevo_apellido']);
    $telefono = trim($_POST['nuevo_telefono']);
    $correo = trim($_POST['nuevo_correo']);

    try {
    $stmt = $conn->prepare("CALL sp_actualizarPerfil(?, ?, ?, ?, ?)");
    $stmt->execute([$id, $nombre, $apellido, $telefono, $correo]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if ($res && $res['estado'] === 'EXITO') {
        header("Location: cuenta.php?msj=edit_ok");
    } else {
        header("Location: cuenta.php?msj=edit_error&error_text=" . urlencode($res['mensaje']));
    }
} catch (PDOException $e) {
    header("Location: cuenta.php?msj=edit_error&error_text=Error de servidor");
}
    exit();
}