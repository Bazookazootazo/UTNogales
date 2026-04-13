<?php
session_start();
include '../config/conexion.php';

// Protección: Solo el ADMIN puede hacer modificaciones
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    header("Location: ../carrerasPRUEBA.php");
    exit();
}

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
        
        // Recibimos los arreglos de checkboxes
        $patrocinadores = isset($_POST['patrocinadores']) ? $_POST['patrocinadores'] : []; 
        $categorias = isset($_POST['categorias']) ? $_POST['categorias'] : []; 
        
        $imagen = null;

        // 1. Manejo de la imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $imagen = "carrera_" . time() . "." . $ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], "../assets/img/carreras/" . $imagen);
        }

        try {
            // INICIAMOS LA TRANSACCIÓN DE SEGURIDAD
            $conn->beginTransaction();

            // 2. Llamar al SP para insertar la carrera base
            $stmt = $conn->prepare("CALL sp_insertar_carrera(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @id_insertado)");
            $stmt->execute([$nombre, $pista, $fecha, $hora, $desc, $vueltas, $km, $costo, $cupo, $estado_evento, $imagen]);
            $stmt->closeCursor(); // Liberamos cursor para hacer la siguiente consulta

            // 3. Obtener el ID de la carrera recién creada
            $res = $conn->query("SELECT @id_insertado AS id_carrera")->fetch(PDO::FETCH_ASSOC);
            $id_nueva_carrera = $res['id_carrera'];

            if ($id_nueva_carrera) {
                // 4. Insertar Patrocinadores marcados
                if (count($patrocinadores) > 0) {
                    $stmt_patro = $conn->prepare("INSERT INTO carreras_patrocinadores (numeroCarrera, numeroPatrocinador) VALUES (?, ?)");
                    foreach ($patrocinadores as $id_patrocinador) {
                        $stmt_patro->execute([$id_nueva_carrera, $id_patrocinador]);
                    }
                }

                // 5. Insertar Categorías marcadas
                if (count($categorias) > 0) {
                    $stmt_cat = $conn->prepare("INSERT INTO carreras_categorias (numeroCarrera, numeroCategoria) VALUES (?, ?)");
                    foreach ($categorias as $id_cat) {
                        $stmt_cat->execute([$id_nueva_carrera, $id_cat]);
                    }
                }
            }

            // CONFIRMAMOS LA TRANSACCIÓN
            $conn->commit();
            
            // Redirección clásica a la página de carreras
            header("Location: ../carrerasPRUEBA.php?msg=carrera_creada");
            exit();

        } catch (PDOException $e) {
            // SI ALGO FALLA, DESHACEMOS TODO (No se crea la carrera ni nada)
            $conn->rollBack();
            die("Error en BD: " . $e->getMessage());
        }
    }
}
?>