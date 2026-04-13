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
    
    // Recibir los arreglos de checkboxes
    $patrocinadores = isset($_POST['patrocinadores']) ? $_POST['patrocinadores'] : [];
    $categorias = isset($_POST['categorias']) ? $_POST['categorias'] : [];
    
    $imagen = null;
    // Lógica de imagen (solo si el admin subió una nueva)
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen = "carrera_" . time() . "." . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], "../assets/img/carreras/" . $imagen);
    }

    try {
        // Iniciamos transacción de seguridad
        $conn->beginTransaction();

        // 1. Actualizar datos base (usando tu SP actual)
        $stmt = $conn->prepare("CALL sp_actualizar_carrera(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $nombre, $pista, $fecha, $hora, $desc, $vueltas, $km, $costo, $cupo, $estado_evento, $imagen]);
        $stmt->closeCursor();

        // 2. Actualizar Patrocinadores (Se borran los viejos y se insertan los marcados)
        $conn->prepare("DELETE FROM carreras_patrocinadores WHERE numeroCarrera = ?")->execute([$id]);
        if (count($patrocinadores) > 0) {
            $stmt_patro = $conn->prepare("INSERT INTO carreras_patrocinadores (numeroCarrera, numeroPatrocinador) VALUES (?, ?)");
            foreach ($patrocinadores as $id_pat) {
                $stmt_patro->execute([$id, $id_pat]);
            }
        }

        // 3. Actualizar Categorías (Se borran las viejas y se insertan las marcadas)
        $conn->prepare("DELETE FROM carreras_categorias WHERE numeroCarrera = ?")->execute([$id]);
        if (count($categorias) > 0) {
            $stmt_cat = $conn->prepare("INSERT INTO carreras_categorias (numeroCarrera, numeroCategoria) VALUES (?, ?)");
            foreach ($categorias as $id_cat) {
                $stmt_cat->execute([$id, $id_cat]);
            }
        }

        // Confirmamos y guardamos todo
        $conn->commit();
        
        // Redirigimos de vuelta a la página con el mensaje de éxito
        header("Location: ../carrerasPRUEBA.php?msg=actualizado_ok");
        exit();

    } catch (PDOException $e) {
        $conn->rollBack();
        die("Error al actualizar: " . $e->getMessage());
    }
}
?>