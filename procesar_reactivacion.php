<?php
session_start();
include 'conexion.php';

if (isset($_SESSION['id_reactivar'])) {
    $id = $_SESSION['id_reactivar'];
    
    // 1. Limpiamos estatus y fecha_baja para detener el borrado
    $query_reactivar = "UPDATE usuarios SET estatus = 'Activo', fecha_baja = NULL WHERE numeroUser = '$id'";
    
    if (mysqli_query($con, $query_reactivar)) {
        
        $query_rol = "SELECT rol FROM usuarios WHERE numeroUser = '$id'";
        $resultado_rol = mysqli_query($con, $query_rol);
        $user = mysqli_fetch_assoc($resultado_rol);

        $_SESSION['id_usuario'] = $id; 
        $_SESSION['rol'] = $user['rol']; 

        unset($_SESSION['id_reactivar']); 
        header("Location: inicio.php?msg=bienvenido_de_nuevo_ok");
    } else {
        header("Location: index.php?error=Error al reactivar cuenta.");
    }
} else {
    header("Location: index.php");
}
exit();
?>