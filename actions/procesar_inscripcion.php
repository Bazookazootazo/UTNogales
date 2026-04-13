<?php
// ============================================================
//  MTB NOGALES — Procesador de Inscripciones
//  Ruta: actions/procesar_inscripcion.php
//  Método: POST (JSON)
//  Acciones: inscribir | cancelar | actualizar_pago (ADMIN)
// ============================================================

session_start();
include '../config/conexion.php';
header('Content-Type: application/json');
ob_start();

$res = ['status' => 'error', 'message' => 'Solicitud no válida.'];

// Verificar sesión activa
if (!isset($_SESSION['id_usuario'])) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Sesión expirada. Por favor inicia sesión.']);
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];
$rol        = $_SESSION['rol'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // ──────────────────────────────────────────────────────────
    //  ACCIÓN: inscribir
    //  Disponible para CICLISTA
    // ──────────────────────────────────────────────────────────
    if ($accion === 'inscribir' && $rol === 'CICLISTA') {

        $id_carrera   = (int) ($_POST['id_carrera']   ?? 0);
        $id_categoria = (int) ($_POST['id_categoria'] ?? 0);

        if ($id_carrera === 0 || $id_categoria === 0) {
            $res = ['status' => 'error', 'message' => 'Datos incompletos. Selecciona carrera y categoría.'];
        } else {
            try {
                $stmt = $conn->prepare("CALL sp_inscribir_ciclista(?, ?, ?, @resultado, @mensaje)");
                $stmt->execute([$id_usuario, $id_carrera, $id_categoria]);

                do { $stmt->closeCursor(); } while ($stmt->nextRowset());

                $out = $conn->query("SELECT @resultado AS res, @mensaje AS msg")->fetch(PDO::FETCH_ASSOC);

                if ($out && $out['res'] == 1) {
                    $res = ['status' => 'success', 'message' => $out['msg']];
                } else {
                    $res = ['status' => 'error', 'message' => $out['msg'] ?? 'No se pudo completar la inscripción.'];
                }
            } catch (Exception $e) {
                $res = ['status' => 'error', 'message' => 'Error interno: ' . $e->getMessage()];
            }
        }

    // ──────────────────────────────────────────────────────────
    //  ACCIÓN: cancelar
    //  Disponible para CICLISTA (solo sus inscripciones)
    // ──────────────────────────────────────────────────────────
    } elseif ($accion === 'cancelar' && $rol === 'CICLISTA') {

        $id_inscripcion = (int) ($_POST['id_inscripcion'] ?? 0);

        if ($id_inscripcion === 0) {
            $res = ['status' => 'error', 'message' => 'ID de inscripción inválido.'];
        } else {
            try {
                $stmt = $conn->prepare("CALL sp_cancelar_inscripcion(?, ?, @resultado, @mensaje)");
                $stmt->execute([$id_inscripcion, $id_usuario]);

                do { $stmt->closeCursor(); } while ($stmt->nextRowset());

                $out = $conn->query("SELECT @resultado AS res, @mensaje AS msg")->fetch(PDO::FETCH_ASSOC);

                if ($out && $out['res'] == 1) {
                    $res = ['status' => 'success', 'message' => $out['msg']];
                } else {
                    $res = ['status' => 'error', 'message' => $out['msg'] ?? 'No se pudo cancelar la inscripción.'];
                }
            } catch (Exception $e) {
                $res = ['status' => 'error', 'message' => 'Error interno: ' . $e->getMessage()];
            }
        }

    // ──────────────────────────────────────────────────────────
    //  ACCIÓN: actualizar_pago
    //  Solo disponible para ADMIN
    // ──────────────────────────────────────────────────────────
    } elseif ($accion === 'actualizar_pago' && $rol === 'ADMIN') {

        $id_inscripcion = (int) ($_POST['id_inscripcion'] ?? 0);
        $nuevo_estado   = trim($_POST['estado_pago']    ?? '');

        if ($id_inscripcion === 0 || empty($nuevo_estado)) {
            $res = ['status' => 'error', 'message' => 'Datos incompletos.'];
        } else {
            try {
                $stmt = $conn->prepare("CALL sp_actualizar_pago(?, ?, @resultado, @mensaje)");
                $stmt->execute([$id_inscripcion, $nuevo_estado]);

                do { $stmt->closeCursor(); } while ($stmt->nextRowset());

                $out = $conn->query("SELECT @resultado AS res, @mensaje AS msg")->fetch(PDO::FETCH_ASSOC);

                if ($out && $out['res'] == 1) {
                    $res = ['status' => 'success', 'message' => $out['msg']];
                } else {
                    $res = ['status' => 'error', 'message' => $out['msg'] ?? 'No se pudo actualizar el pago.'];
                }
            } catch (Exception $e) {
                $res = ['status' => 'error', 'message' => 'Error interno: ' . $e->getMessage()];
            }
        }

    } else {
        $res = ['status' => 'error', 'message' => 'Acción no permitida o rol incorrecto.'];
    }
}

ob_end_clean();
echo json_encode($res);
exit;
