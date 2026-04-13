<?php
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $estatus = $_POST['estatus'];

    try {
        $stmt = $conn->prepare("UPDATE patrocinador SET estatus = ? WHERE numeroPatrocinador = ?");
        $stmt->execute([$estatus, $id]);

        echo json_encode([
            'status' => 'success', 
            'message' => 'El estatus ha sido actualizado correctamente.'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error al actualizar: ' . $e->getMessage()
        ]);
    }
}