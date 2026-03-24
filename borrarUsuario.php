<?php 
require_once("conexion.php");
session_start();
if (isset($_GET['numeroUser'])) {
    $idABorrar = mysqli_real_escape_string($con, $_GET['numeroUser']);
    $idSesionActual = $_SESSION['numeroUser'];

    if ($idABorrar == $idSesionActual) {
        echo "<script>alert('No puedes eliminar tu propia cuenta mientras estás en sesión.'); window.location='administracionCuentas.php';</script>";
        exit();
    }

    $query = "DELETE FROM usuarios WHERE idUs = '$idABorrar'";
    $result = mysqli_query($con, $query);

    if ($result) {
        header("location:administracionCuentas.php?msj=usuario_eliminado");
    } else {
        echo "Error al intentar borrar el registro: " . mysqli_error($con);
    }
} else {
    header("location:administracionCuentas.php");
}
?>