<?php
require_once "../config/conexion.php"; 

if (isset($_GET['id']) && isset($_GET['estado'])) {
    $id = $_GET['id'];
    $estado = $_GET['estado'];

    try {
        $stmt = $conn->prepare("CALL sp_estatus_pista(?, ?)");
        $stmt->execute([$id, $estado]);
        header("Location: ../pistasPRUEBA.php");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}