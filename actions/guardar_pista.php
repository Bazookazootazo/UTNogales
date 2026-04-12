<?php
include '../config/conexion.php';
header('Content-Type: application/json');
ob_start();

$res = ["status" => "error", "message" => "Error de conexión"];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombrePista'] ?? '';
    $seccion = $_POST['seccion'] ?? '';
    $estado = $_POST['estadoPista'] ?? 'Abierta';
    $imagen = "default_pista.png";

    if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] == 0) {
        $ext = pathinfo($_FILES['imagen_archivo']['name'], PATHINFO_EXTENSION);
        $imagen = time() . "_" . preg_replace('/[^a-zA-Z0-9]/', '', $nombre) . "." . $ext;
        move_uploaded_file($_FILES['imagen_archivo']['tmp_name'], "../assets/img/pistas/" . $imagen);
    }

    try {
        $stmt = $conn->prepare("CALL sp_insertar_pista(?, ?, ?, ?, @resultado, @mensaje)");
        $stmt->execute([$nombre, $seccion, $estado, $imagen]);

        do { $stmt->closeCursor(); } while ($stmt->nextRowset());

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
ob_end_clean();
echo json_encode($res);
exit;