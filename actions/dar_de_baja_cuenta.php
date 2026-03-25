<?php 
require_once(__DIR__ . "/../config/conexion.php"); 
session_start();

if (isset($_GET['numeroUser'])) {
    $idABorrar = mysqli_real_escape_string($con, $_GET['numeroUser']);
    $idSesionActual = $_SESSION['id_usuario']; 
    $rolSesion = $_SESSION['rol'];

    // 1. LEER EL NUEVO ESTATUS (Si no viene nada, por defecto es INACTIVO)
    $nuevoEstatus = isset($_GET['nuevo_estatus']) ? mysqli_real_escape_string($con, $_GET['nuevo_estatus']) : 'INACTIVO';

    if ($idABorrar == $idSesionActual && $rolSesion == 'ADMIN') {
        header("location: ../cuenta.php?msj=error_admin");
        exit();
    }

    $fechaHoy = date("Y-m-d H:i:s");
    
    // 2. ACTUALIZAR SEGÚN EL ESTATUS QUE MANDAMOS
    // Si es Activo, podemos limpiar la fecha de baja si quieres, o dejarla como registro
    $query = "UPDATE usuarios SET estatus = '$nuevoEstatus', fecha_baja = '$fechaHoy' WHERE numeroUser = '$idABorrar'";
    $result = mysqli_query($con, $query);

    if ($result) {
        if ($idABorrar == $idSesionActual) {
            session_destroy();
            header("location: ../index.php?msj=cuenta_desactivada");
            exit();
        } else {
            // Regresamos a la administración con un mensaje diferente según lo que se hizo
            $mensaje = ($nuevoEstatus == 'ACTIVO') ? "usuario_reactivado" : "usuario_desactivado";
            header("location: ../administracion_de_usuarios.php?msj=$mensaje");
            exit();
        }
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>