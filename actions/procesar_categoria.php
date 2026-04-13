<?php
session_start();
include '../config/conexion.php';

// Protección: Solo el ADMIN puede hacer modificaciones
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    header("Location: ../carrerasPRUEBA.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $genero = $_POST['genero'];
    $min = (int)$_POST['min'];
    $max = (int)$_POST['max'];

    try {
        // Insertamos la nueva categoría en la base de datos
        $stmt = $conn->prepare("INSERT INTO categorias (tipoCategoria, generoCategoria, edadMinima, edadMaxima) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $genero, $min, $max]);
        
        // Redirigimos de vuelta con el mensaje de éxito
        header("Location: ../carrerasPRUEBA.php?msg=categoria_creada");
        exit();

    } catch (PDOException $e) {
        die("Error al guardar la categoría: " . $e->getMessage());
    }
}
?>