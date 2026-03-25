<?php 
require_once("../config/conexion.php");
session_start();

if (isset($_GET['numeroUser'])) {
    $idABorrar = mysqli_real_escape_string($con, $_GET['numeroUser']);
    $idSesionActual = $_SESSION['id_usuario']; 
    $rolSesion = $_SESSION['rol'];

    if ($idABorrar == $idSesionActual && $rolSesion == 'ADMIN') {
        header("location: ../cuenta.php?msj=error_admin");
        exit();
    }

    $fechaHoy = date("Y-m-d H:i:s");
    $query = "UPDATE usuarios SET estatus = 'INACTIVO', fecha_baja = '$fechaHoy' WHERE numeroUser = '$idABorrar'";
    $result = mysqli_query($con, $query);

    if ($result) {
        if ($idABorrar == $idSesionActual) {
            session_destroy();
            header("location: ../registro.php?msj=cuenta_desactivada");
            exit();
        } else {
            header("location: ../administracionCuentas.php?msj=usuario_desactivado");
            exit();
        }
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>