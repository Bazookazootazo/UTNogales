<?php
session_start();
include 'config/conexion.php';

$response = ['activo' => false];

if (isset($_SESSION['id_usuario'])) {
    $id = $_SESSION['id_usuario'];
    $query = "SELECT estatus FROM usuarios WHERE numeroUser = '$id'";
    $res = mysqli_query($con, $query);
    $user = mysqli_fetch_assoc($res);

    if ($user && strtoupper($user['estatus']) !== 'INACTIVO') {
        $response['activo'] = true;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit();