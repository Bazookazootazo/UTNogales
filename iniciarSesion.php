<?php
session_start();
include 'conexion.php';

if (isset($_POST['submit'])) {
    $nombre = $_POST['nombre'] ?? '';
    $contra = $_POST['contraseña'] ?? '';

    try {
        $stmt = $conn->prepare("CALL sp_login(?, ?)");
        $stmt->execute([$nombre, $contra]);
        
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Limpiamos resultados extra para evitar errores de conexión
        while ($stmt->nextRowset()) { }
        $stmt->closeCursor();

        if ($res && $res['estado'] === 'EXITO') {
            $_SESSION['id_usuario'] = $res['id_usuario'];
            $_SESSION['rol']        = $res['rol'];
            header("Location: inicio.php?msg=" . urlencode($res['mensaje']));
            exit();
        } else {
            // El SP mandó un mensaje de error específico
            $error = $res['mensaje'] ?? 'Error desconocido en el servidor';
            header("Location: index.php?error=" . urlencode($error));
            exit();
        }
    } catch (PDOException $e) {
        header("Location: index.php?error=" . urlencode("Error de base de datos: " . $e->getMessage()));
        exit();
    }
}
?>