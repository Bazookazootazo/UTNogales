<?php
require_once "../config/conexion.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['numeroPatrocinador'];
    $nombre = trim($_POST['nombrePatrocinador']);
    $contacto = trim($_POST['contactoPatrocinador']);
    $nombre_logo = null;

    // Manejo de la subida del Logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $nombre_logo = "logo_" . $id . "_" . time() . "." . $ext;
        $ruta_destino = "../assets/img/patrocinadores/" . $nombre_logo;
        
        move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_destino);
    }

    try {
        $stmt = $conn->prepare("CALL sp_actualizar_patrocinador(?, ?, ?, ?, ?)");
        $stmt->execute([$id, $nombre, $contacto, $telefono, $nombre_logo]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado['estado'] === 'EXITO') {
            header("Location: ../patrocinadores.php?msg=actualizado_ok");
        } else {
            header("Location: ../patrocinadores.php?error=" . urlencode($resultado['mensaje']));
        }

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}