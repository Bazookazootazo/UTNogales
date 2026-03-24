<?php 
require_once("conexion.php");
session_start();

if (isset($_GET['numeroUser'])) {
    $idABorrar = mysqli_real_escape_string($con, $_GET['numeroUser']);
    $idSesionActual = $_SESSION['numeroUser'];
    $rolSesion = $_SESSION['rol']; // Asumiendo que guardas el rol en la sesión

    // Nueva lógica: Solo los ADMIN tienen prohibido el autoborrado
    if ($idABorrar == $idSesionActual && $rolSesion == 'ADMIN') {
        header("location:cuenta.php?msj=error_admin_autoborrado");
        exit();
    }

    // Si llegó aquí y es CICLISTA (o es otro ID), procedemos
    // Usamos el nombre de columna 'numeroUser' visto en tu base de datos
    $query = "DELETE FROM usuarios WHERE numeroUser = '$idABorrar'";
    $result = mysqli_query($con, $query);

    if ($result) {
        // Si un ciclista se borra a sí mismo, destruimos la sesión
        if ($idABorrar == $idSesionActual) {
            session_destroy();
            header("location:login.php?msj=cuenta_eliminada");
        } else {
            header("location:administracionCuentas.php?msj=usuario_eliminado");
        }
    } else {
        echo "Error al intentar borrar el registro: " . mysqli_error($con);
    }
} else {
    header("location:cuenta.php");
}
?>