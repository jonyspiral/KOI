<?php
// content/ws/sucursales.php
// Endpoint mínimo: devuelve sucursales del cliente en JSON (MySQL8, mysqli, PHP 5.6)

@session_start();
header('Content-Type: application/json; charset=utf-8');

$response = array('status' => 500, 'message' => 'Error interno', 'data' => null);

try {
    // Traer config del proyecto (credenciales MySQL)
    require_once(__DIR__ . '/../../includes.php');

    // Obtener idCliente: prioridad a GET/POST, si no, intentar desde sesión/objeto Usuario (si existe)
    $idCliente = isset($_GET['idCliente']) ? $_GET['idCliente'] : (isset($_POST['idCliente']) ? $_POST['idCliente'] : null);

    if ($idCliente === null && class_exists('Usuario')) {
        try {
            $u = Usuario::logueado();
            if ($u && isset($u->cliente) && isset($u->cliente->id)) {
                $idCliente = $u->cliente->id;
            }
        } catch (Exception $e) {
            // Ignorar: puede no haber sesión válida aquí
        }
    }

    if ($idCliente === null || $idCliente === '') {
        http_response_code(400);
        $response['status'] = 400;
        $response['message'] = 'Falta parámetro idCliente o sesión no disponible';
        echo json_encode($response);
        exit;
    }

    // Conexión mysqli (MySQL8)
    $mysqli = @new mysqli(
        Config::mysql_host,
        Config::mysql_user,
        Config::mysql_pass,
        Config::mysql_db,
        Config::mysql_port
    );
    if ($mysqli->connect_errno) {
        http_response_code(500);
        $response['status'] = 500;
        $response['message'] = 'DB connect error: ' . $mysqli->connect_error;
        echo json_encode($response);
        exit;
    }
    // Charset
    if (!$mysqli->set_charset('utf8')) {
        // Continuar pero informar
    }

    // Query exacta (usa vista sucursales_v; WHERE según KOI1: cod_cli + anulado)
    $sql = "SELECT 
                s.cod_sucursal   AS id,
                s.nombre         AS nombre
            FROM sucursales_v s
            WHERE s.cod_cli = ?
              AND s.anulado = 'N'
            ORDER BY s.cod_sucursal";

    if (!($stmt = $mysqli->prepare($sql))) {
        http_response_code(500);
        $response['status'] = 500;
        $response['message'] = 'DB prepare error: ' . $mysqli->error;
        echo json_encode($response);
        exit;
    }

    // idCliente suele ser entero/cadena; bind como string por compatibilidad
    $stmt->bind_param('s', $idCliente);

    if (!$stmt->execute()) {
        http_response_code(500);
        $response['status'] = 500;
        $response['message'] = 'DB execute error: ' . $stmt->error;
        echo json_encode($response);
        $stmt->close();
        $mysqli->close();
        exit;
    }

    $result = $stmt->get_result();
    $rows = array();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Garantizar shape {id, nombre}
            $rows[] = array(
                'id'     => isset($row['id']) ? $row['id'] : null,
                'nombre' => isset($row['nombre']) ? $row['nombre'] : null
            );
        }
        $result->free();
    }

    $stmt->close();
    $mysqli->close();

    http_response_code(200);
    $response['status'] = 200;
    $response['message'] = 'OK';
    $response['data'] = $rows;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $ex) {
    http_response_code(500);
    $response['status'] = 500;
    $response['message'] = 'Exception: ' . $ex->getMessage();
    echo json_encode($response);
}
