<?php 
require_once("../config/conexion.php");
session_start();

if (isset($_GET['numeroUser'])) {
    $idABorrar = mysqli_real_escape_string($con, $_GET['numeroUser']);
    $idSesionActual = $_SESSION['numeroUser'];
    $rolSesion = $_SESSION['rol']; 

    if ($idABorrar == $idSesionActual && $rolSesion == 'ADMIN') {
        header("location: ../cuenta.php?msj=error_admin_autoborrado");
        exit();
    }

    $query = "DELETE FROM usuarios WHERE numeroUser = '$idABorrar'";
    $result = mysqli_query($con, $query);

    if ($result) {
        if ($idABorrar == $idSesionActual) {
            session_destroy();
            header("location: ../login.php?msj=cuenta_eliminada");
        } else {
            header("location: ../administracionCuentas.php?msj=usuario_eliminado");
        }
    } else {
        echo "Error al intentar borrar el registro: " . mysqli_error($con);
    }
} else {
    header("location: ../cuenta.php");
}
?>