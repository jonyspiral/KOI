<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json');

require_once('../../../premaster.php');
if (Usuario::logueado()->puede('cliente/favoritos/borrar/')) {

$contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';

if (stripos($contentType, 'application/json') === 0) {
    $content = trim(file_get_contents('php://input'));
    $decoded = json_decode($content, true);

    if (!is_array($decoded) || !isset($decoded['favorites']) || !is_array($decoded['favorites'])) {
        echo json_encode(array('status' => 400, 'message' => 'Formato invalido', 'data' => array()));
        die;
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

    echo json_encode(array('status' => 200, 'message' => 'success', 'data' => $response));
    die;
}

echo json_encode(array('status' => 400, 'message' => 'Bad Request', 'data' => array()));
die;

}
?>
