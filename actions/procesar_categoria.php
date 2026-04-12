<?php
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $genero = $_POST['genero'];
    $min = $_POST['min'];
    $max = $_POST['max'];

    try {
        $stmt = $conn->prepare("INSERT INTO categorias (tipoCategoria, generoCategoria, edadMinima, edadMaxima) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $genero, $min, $max]);
        header("Location: ../carrerasPRUEBA.php?msg=categoria_creada");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>