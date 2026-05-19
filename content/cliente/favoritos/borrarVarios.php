<?php
if (!ob_get_level()) {
    ob_start();
}

function favoritosBatchJsonResponse($status, $message, $data = array()) {
    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode(array(
        'status' => $status,
        'message' => $message,
        'data' => $data
    ));
    exit;
}

register_shutdown_function(function () {
    $error = error_get_last();
    if (!$error) {
        return;
    }

    $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR);
    if (!in_array($error['type'], $fatalTypes, true)) {
        return;
    }

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode(array(
        'status' => 500,
        'message' => 'Fatal error',
        'data' => array(
            'type' => $error['type'],
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        )
    ));
});

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json');

require_once('../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

try {
    $usuario = Usuario::logueado();
    if (!$usuario || !$usuario->puede('cliente/favoritos/borrar/')) {
        favoritosBatchJsonResponse(403, 'Permiso denegado o usuario no logueado');
    }

    $contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';

    if (stripos($contentType, 'application/json') !== 0) {
        favoritosBatchJsonResponse(400, 'Bad Request');
    }

    $content = trim(file_get_contents('php://input'));
    $decoded = json_decode($content, true);

    if (!is_array($decoded) || !isset($decoded['favorites']) || !is_array($decoded['favorites'])) {
        favoritosBatchJsonResponse(400, 'Formato invalido');
    }

    $response = array();
    foreach ($decoded['favorites'] as $fav) {
        try {
            $idCliente = Usuario::logueado()->cliente->id;
            $favorito = FavoritoCliente::find($idCliente, $fav['idArticulo'], $fav['idColorPorArticulo']);
            $favorito->borrar();

            $response[] = array(
                'idArticulo' => $fav['idArticulo'],
                'idColorPorArticulo' => $fav['idColorPorArticulo'],
                'saved' => true,
                'message' => 'Guardado'
            );
        } catch (FactoryExceptionRegistroNoExistente $ex) {
            $response[] = array(
                'idArticulo' => $fav['idArticulo'],
                'idColorPorArticulo' => $fav['idColorPorArticulo'],
                'saved' => true,
                'message' => 'Ya estaba Guardado'
            );
        } catch (Exception $ex) {
            $response[] = array(
                'idArticulo' => isset($fav['idArticulo']) ? $fav['idArticulo'] : null,
                'idColorPorArticulo' => isset($fav['idColorPorArticulo']) ? $fav['idColorPorArticulo'] : null,
                'saved' => false,
                'message' => $ex->getMessage()
            );
        }
    }

    favoritosBatchJsonResponse(200, 'success', $response);
} catch (Exception $ex) {
    favoritosBatchJsonResponse(500, $ex->getMessage());
}
?>
