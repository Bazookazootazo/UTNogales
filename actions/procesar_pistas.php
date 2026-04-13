<?php
session_start(); 
// Como este archivo está dentro de 'actions', subimos un nivel para llegar a 'config'
include '../config/conexion.php'; 

// Protección: Solo el ADMIN puede hacer modificaciones
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    header("Location: ../pistasPRUEBA.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'crear') {
            $nombre = trim($_POST['nombrePista']);
            $seccion = trim($_POST['seccion']);

            $stmt = $conn->prepare("INSERT INTO pistas (nombrePista, seccion) VALUES (?, ?)");
            $stmt->execute([$nombre, $seccion]);

            $_SESSION['swal'] = ['icon' => 'success', 'title' => '¡Éxito!', 'text' => 'Pista registrada correctamente.'];

        } elseif ($accion === 'editar') {
            $id = $_POST['numeroPista'];
            $nombre = trim($_POST['nombrePista']);
            $seccion = trim($_POST['seccion']);

            $stmt = $conn->prepare("UPDATE pistas SET nombrePista = ?, seccion = ? WHERE numeroPista = ?");
            $stmt->execute([$nombre, $seccion, $id]);

            $_SESSION['swal'] = ['icon' => 'success', 'title' => 'Actualizado', 'text' => 'Los datos de la pista se actualizaron.'];

        } elseif ($accion === 'eliminar') {
            $id = $_POST['numeroPista'];

            // Opcional: Podrías verificar si hay carreras usando esta pista antes de eliminarla
            $stmt = $conn->prepare("DELETE FROM pistas WHERE numeroPista = ?");
            $stmt->execute([$id]);

            $_SESSION['swal'] = ['icon' => 'info', 'title' => 'Eliminado', 'text' => 'La pista ha sido borrada del sistema.'];
        }
    } catch (PDOException $e) {
        // Código 23000 es el error de llave foránea (si intentas borrar una pista que ya tiene carreras)
        if ($e->getCode() == 23000) {
            $_SESSION['swal'] = ['icon' => 'error', 'title' => 'No se puede eliminar', 'text' => 'Esta pista está siendo utilizada en una o más carreras.'];
        } else {
            $_SESSION['swal'] = ['icon' => 'error', 'title' => 'Error de Base de Datos', 'text' => 'Ocurrió un problema técnico.'];
        }
    }

    // Terminamos y regresamos a la vista
    header("Location: ../pistasPRUEBA.php");
    exit();
}
?>