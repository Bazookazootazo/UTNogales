<?php
session_start();
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    
    if ($_POST['accion'] === 'crear') {
        $nombre = trim($_POST['nombre']);
        $pista = $_POST['pista'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $estado_evento = $_POST['estado_evento'];
        $km = $_POST['km'];
        $vueltas = $_POST['vueltas'];
        $cupo = $_POST['cupo'];
        $costo = $_POST['costo'];
        $desc = trim($_POST['descripcion']);
        $patrocinadores = isset($_POST['patrocinadores']) ? $_POST['patrocinadores'] : []; // Array de IDs
        $imagen = null;

        // 1. Manejo de la imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $imagen = "carrera_" . time() . "." . $ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], "../assets/img/carreras/" . $imagen);
        }

        try {
            // 2. Llamar al SP para insertar la carrera base
            $stmt = $conn->prepare("CALL sp_insertar_carrera(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @id_insertado)");
            $stmt->execute([$nombre, $pista, $fecha, $hora, $desc, $vueltas, $km, $costo, $cupo, $estado_evento, $imagen]);
            
            // Liberar el cursor para poder hacer el siguiente SELECT
            $stmt->closeCursor();

            // 3. Obtener el ID de la carrera que se acaba de crear
            $res = $conn->query("SELECT @id_insertado AS id_carrera")->fetch(PDO::FETCH_ASSOC);
            $id_nueva_carrera = $res['id_carrera'];

            // 4. Insertar los Patrocinadores Múltiples (Si seleccionó alguno)
            if ($id_nueva_carrera && count($patrocinadores) > 0) {
                // Preparamos la consulta para la tabla puente
                $stmt_patro = $conn->prepare("INSERT INTO carreras_patrocinadores (numeroCarrera, numeroPatrocinador) VALUES (?, ?)");
                
                // Ciclo para insertar cada check que el admin marcó
                foreach ($patrocinadores as $id_patrocinador) {
                    $stmt_patro->execute([$id_nueva_carrera, $id_patrocinador]);
                }
            }

            header("Location: ../carrerasPRUEBA.php?msg=carrera_creada");
            exit();

        } catch (PDOException $e) {
            die("Error en BD: " . $e->getMessage());
        }
    }
}
?>