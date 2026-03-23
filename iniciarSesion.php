<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "mtbnog";
$con = new mysqli($servername, $username, $password, $database);
if ($con->connect_error) {
    die("Error al conectar a Usuarios: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nombre'];
    $contraseña = $_POST['contraseña']; 
    $stmt = $con->prepare("SELECT idUs, clave, nombre, contraseña, correo, rol, estatus FROM usuarios WHERE clave = ?");
    
    if ($stmt === false) {
        echo "<script>alert('Error interno al preparar la consulta.'); window.location='index.php';</script>";
        $con->close();
        exit();
    } 
    
    $stmt->bind_param("s", $nom);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($idUS_db, $clave_db, $nom_db, $corr_db, $est_db, $contraseña_db, $rol_db);
        $stmt->fetch();
        
        if (password_verify($contraseña, $contraseña_db ?? '')) {
            $_SESSION["loggedin"] = true;
            $_SESSION['idUs'] = $idUS_db;
            $_SESSION['clave'] = $clave_db; 
            $_SESSION['nombre'] = $nom_db;
            $_SESSION['correo'] = $corr_db;
            $_SESSION['rol'] = $rol_db;
            $_SESSION['estatus'] = $est_db;
            
            $stmt->close();
            $con->close();
            header("Location: inicio.php");
            exit(); 
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.location='index.php';</script>";
    }
    $stmt->close();
}
if (isset($con) && $con instanceof mysqli) {
    $con->close();
}
?>