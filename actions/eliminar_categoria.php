<?php
session_start();
require_once "../config/conexion.php"; 

// Protección: Solo el ADMIN puede eliminar
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    header("Location: ../carrerasPRUEBA.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Intentamos eliminar la categoría
        $stmt = $conn->prepare("DELETE FROM categorias WHERE numeroCategoria = ?");
        $stmt->execute([$id]);
        
        // Si tiene éxito, redirigimos con la bandera de "categoria_eliminada"
        header("Location: ../carrerasPRUEBA.php?msg=categoria_eliminada");
        exit();

    } catch (PDOException $e) {
        // El código 23000 es una restricción de llave foránea de MySQL
        // Significa que la categoría ya está en uso en otra tabla y no se puede borrar
        if ($e->getCode() == 23000) {
            echo "<script>
                    alert('Acción denegada: No puedes eliminar esta categoría porque ya está asignada a una carrera o tiene ciclistas inscritos.');
                    window.location.href = '../carrerasPRUEBA.php';
                  </script>";
        } else {
            die("Error interno al eliminar: " . $e->getMessage());
        }
    }
} else {
    // Si entran al archivo sin un ID, los regresamos
    header("Location: ../carrerasPRUEBA.php");
    exit();
}
?>