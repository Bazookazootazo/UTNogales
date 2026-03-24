<?php
session_start();
require 'conexion.php'; 

if (isset($_POST['submit'])) {
    $correo = $_POST['nombre'] ?? ''; 
    $pass_escrita = $_POST['contraseña'] ?? '';

    try {
        $stmt = $conn->prepare("CALL sp_login(?)");
        $stmt->execute([$correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // VERIFICACIÓN TÉCNICA
            if (password_verify($pass_escrita, $user['hash'])) {
                $_SESSION['id_usuario'] = $user['id'];
                $_SESSION['rol'] = $user['rol'];
                 header("Location: inicio.php?msg=ok");
                exit();
            } else {
                // Esto te dirá si el hash está mal guardado
                header("Location: index.php?error=La contraseña no coincide con el hash guardado.");
            }
        } else {
            header("Location: index.php?error=El correo no existe en la BD.");
        }
    } catch (PDOException $e) {
        die("Error de SQL: " . $e->getMessage());
    }
}