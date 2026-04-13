<?php
require_once "../config/conexion.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['numeroPatrocinador'];
    $nombre = trim($_POST['nombrePatrocinador']);
    $contacto = trim($_POST['contactoPatrocinador']);
    $nombre_logo = "";

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $nombre_logo = "logo_" . $id . "_" . time() . "." . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], "../assets/img/patrocinadores/" . $nombre_logo);
    }

    try {
        $stmt = $conn->prepare("CALL sp_actualizar_patrocinador(?, ?, ?, ?)");
        $stmt->execute([$id, $nombre, $contacto, $nombre_logo]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

if ($res['estado'] === 'EXITO') {
    header("Location: ../patrocinadores.php?msg=actualizado_ok");
} else {
    // Esto enviará el mensaje exacto del SP (ej: "Este número de contacto ya pertenece...")
    header("Location: ../patrocinadores.php?error=" . urlencode($res['mensaje']));
}
    } catch (PDOException $e) {
        header("Location: ../patrocinadores.php?error=" . urlencode($e->getMessage()));
    }
}