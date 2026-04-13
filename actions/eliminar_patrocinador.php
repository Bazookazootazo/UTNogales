<?php
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // 1. Opcional: Podrías buscar el nombre del logo para borrar el archivo del servidor
        $stmt_logo = $conn->prepare("SELECT logo_patrocinador FROM patrocinador WHERE numeroPatrocinador = ?");
        $stmt_logo->execute([$id]);
        $logo = $stmt_logo->fetchColumn();

        // 2. Borrar el registro
        $stmt = $conn->prepare("DELETE FROM patrocinador WHERE numeroPatrocinador = ?");
        $stmt->execute([$id]);

        // 3. Borrar el archivo físico si no es el default
        if ($logo && $logo != 'default_logo.png') {
            @unlink("../assets/img/patrocinadores/" . $logo);
        }

        echo json_encode(['status' => 'success', 'message' => 'Patrocinador eliminado correctamente.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar: ' . $e->getMessage()]);
    }
}