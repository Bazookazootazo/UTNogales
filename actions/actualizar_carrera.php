<?php
session_start();
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_carrera'];
    $nombre = trim($_POST['nombre']);
    $pista = $_POST['pista'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $estado_evento = $_POST['estado_evento'];
    $km = $_POST['km'];
    $vueltas = $_POST['vueltas'];
    $cupo = $_POST['cupo'];
    $costo = $_POST['costo'];
    $desc = trim($_POST['descripcion']);
    $patrocinadores = isset($_POST['patrocinadores']) ? $_POST['patrocinadores'] : [];
    
    $imagen = null;
    // Lógica de imagen (solo si subió una nueva)
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen = "carrera_" . time() . "." . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], "../assets/img/carreras/" . $imagen);
    }

    try {
        $conn->beginTransaction();

        // 1. Actualizar datos base (usando el SP que ya tienes o un UPDATE directo)
        $stmt = $conn->prepare("CALL sp_actualizar_carrera(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $nombre, $pista, $fecha, $hora, $desc, $vueltas, $km, $costo, $cupo, $estado_evento, $imagen]);
        $stmt->closeCursor();

        // 2. Actualizar Patrocinadores (Limpiar y Re-insertar)
        $conn->prepare("DELETE FROM carreras_patrocinadores WHERE numeroCarrera = ?")->execute([$id]);

        if (count($patrocinadores) > 0) {
            $stmt_patro = $conn->prepare("INSERT INTO carreras_patrocinadores (numeroCarrera, numeroPatrocinador) VALUES (?, ?)");
            foreach ($patrocinadores as $id_pat) {
                $stmt_patro->execute([$id, $id_pat]);
            }
        }

        $conn->commit();
        header("Location: ../carrerasPRUEBA.php?msg=actualizado_ok");

    } catch (PDOException $e) {
        $conn->rollBack();
        die("Error al actualizar: " . $e->getMessage());
    }
}