<?php 
require_once("conexion.php");
session_start();

if (isset($_GET['numeroUser'])) {
    $idABorrar = mysqli_real_escape_string($con, $_GET['numeroUser']);
    $nuevoEstatus = isset($_GET['nuevo_estatus']) ? mysqli_real_escape_string($con, $_GET['nuevo_estatus']) : 'Inactivo'; 
    $idSesionActual = $_SESSION['id_usuario']; 
    $rolSesion = $_SESSION['rol'];

    if ($idABorrar == $idSesionActual && $rolSesion == 'ADMIN') {
        header("location:cuenta.php?msj=error_admin");
        exit();
    }

    if (strtoupper($nuevoEstatus) == 'ACTIVO') {
        $query = "UPDATE usuarios SET estatus = 'Activo', fecha_baja = NULL WHERE numeroUser = '$idABorrar'";
    } else {
        $fechaHoy = date("Y-m-d H:i:s");
        $query = "UPDATE usuarios SET estatus = 'Inactivo', fecha_baja = '$fechaHoy' WHERE numeroUser = '$idABorrar'";
    }

    $result = mysqli_query($con, $query);

    if ($result) {
        if ($idABorrar == $idSesionActual) {
            session_destroy();
            header("location:index.php?msj=cuenta_desactivada");
            exit();
        } else {
            $mensaje = (strtoupper($nuevoEstatus) == 'ACTIVO') ? 'alta_ok' : 'baja_ok';
            header("location:administracion_de_usuarios.php?msg=$mensaje");
            exit();
        }
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>