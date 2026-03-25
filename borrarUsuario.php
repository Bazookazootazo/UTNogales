<?php 
require_once("config/conexion.php"); 
session_start();

if (isset($_GET['numeroUser'])) {
    $idABorrar = $_GET['numeroUser'];
    
    $idSesionActual = $_SESSION["id_usuario"] ?? null; 
    $rolSesion = $_SESSION['rol'] ?? ''; 

    if ($idABorrar == $idSesionActual && $rolSesion == 'ADMIN') {
        header("Location: cuenta.php?msj=error_admin_autoborrado");
        exit();
    }

    try {
        $query = "DELETE FROM usuarios WHERE numeroUser = ?";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([$idABorrar]);

        if ($result) {
            if ($idABorrar == $idSesionActual) {
                session_destroy();
                header("Location: login.php?msj=cuenta_eliminada");
            } else {
               header("Location: administracion_de_usuarios.php?msg=borrado_ok");
            exit();
            }
            exit();
        }
    } catch (PDOException $e) {
        die("Error al intentar borrar: " . $e->getMessage());
    }
} else {
    header("Location: administracion_de_usuarios.php");
    exit();
}
?>