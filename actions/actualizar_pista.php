<?php
require_once "../config/conexion.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['numeroPista'];
    $nombre = trim($_POST['nombrePista']);
    $seccion = trim($_POST['seccion']);
    $estado = $_POST['estadoPista'];
    $nombre_imagen = null;

    if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagen_archivo']['name'], PATHINFO_EXTENSION);
        $nombre_imagen = "pista_" . $id . "_" . time() . "." . $ext;
        move_uploaded_file($_FILES['imagen_archivo']['tmp_name'], "../assets/img/pistas/" . $nombre_imagen);
    }

    try {
        $stmt = $conn->prepare("CALL sp_actualizar_pista(?, ?, ?, ?, ?)");
        $stmt->execute([$id, $nombre, $seccion, $estado, $nombre_imagen]);
        
        header("Location: ../pistasPRUEBA.php?msg=actualizado_ok");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}