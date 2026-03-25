<?php
include '../config/conexion.php';
header('Content-Type: application/json');

// Evita que cualquier error de texto se mezcle con el JSON
ob_start();

$res = ["status" => "error", "message" => "Error de conexión"];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombrePatrocinador'] ?? '';
    $contacto = $_POST['contactoPatrocinador'] ?? '';
    $logo = "default_logo.png";

    if (isset($_FILES['logo_archivo']) && $_FILES['logo_archivo']['error'] == 0) {
        $logo = time() . "_" . $_FILES['logo_archivo']['name'];
        move_uploaded_file($_FILES['logo_archivo']['tmp_name'], "../img/patrocinadores/" . $logo);
    }

    try {
        // Ejecución del SP
        $stmt = $conn->prepare("CALL sp_insertar_patrocinador(?, ?, ?, @resultado, @mensaje)");
        $stmt->execute([$nombre, $contacto, $logo]);

        // IMPORTANTE: Limpiar todos los sets de resultados pendientes
        do {
            $stmt->closeCursor();
        } while ($stmt->nextRowset());

        // Ahora pedimos los valores de salida
        $output = $conn->query("SELECT @resultado AS res, @mensaje AS msg")->fetch(PDO::FETCH_ASSOC);

        if ($output && $output['res'] == 1) {
            $res = ["status" => "success", "message" => $output['msg']];
        } else {
            $res = ["status" => "error", "message" => $output['msg'] ?? 'Error en el SP'];
        }
    } catch (Exception $e) {
        $res = ["status" => "error", "message" => "Error: " . $e->getMessage()];
    }
}

// Limpiar el buffer y enviar solo el JSON limpio
ob_end_clean();
echo json_encode($res);
exit;