<?php
session_start();
require_once "../config/conexion.php"; 

// Solo el ADMIN puede eliminar
if (isset($_GET['id']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'ADMIN') {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM categorias WHERE numeroCategoria = ?");
        $stmt->execute([$id]);
        
        header("Location: ../carrerasPRUEBAS.php?msg=categoria_eliminada");
        exit();

    } catch (PDOException $e) {
        // El código 23000 significa que hay una restricción de llave foránea.
        // Es decir, alguien ya está inscrito usando esta categoría.
        if ($e->getCode() == 23000) {
            echo "<script>
                    alert('No se puede eliminar esta categoría porque ya hay ciclistas o inscripciones usándola.');
                    window.location.href = '../carrerasPRUEBA.php';
                  </script>";
        } else {
            die("Error al eliminar: " . $e->getMessage());
        }
    }
} else {
    header("Location: ../carrerasPRUEBA.php");
    exit();
}
?>