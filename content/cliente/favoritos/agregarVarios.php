<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json');

require_once('../../../premaster.php');

if (Usuario::logueado()->puede('cliente/favoritos/agregar/')) {

    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);

    if (!is_array($decoded) || !isset($decoded['favorites'])) {
        echo json_encode(array(
            'status' => 400,
            'message' => 'Formato inválido',
            'data' => array()
        ));
        exit;
    }

    $response = array();

    foreach ($decoded['favorites'] as $fav) {
        try {
            $favorito = FavoritoCliente::find();
            $favorito->cliente = Usuario::logueado()->cliente;
            $favorito->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($fav['idArticulo'], $fav['idColorPorArticulo']);
            $favorito->articulo = $favorito->colorPorArticulo->articulo;

            $favorito->guardar();

            $response[] = array(
                'idArticulo' => $fav['idArticulo'],
                'idColorPorArticulo' => $fav['idColorPorArticulo'],
                'saved' => true,
                'message' => 'Guardado'
            );
        } catch (FactoryExceptionRegistroExistente $ex) {
            $response[] = array(
                'idArticulo' => $fav['idArticulo'],
                'idColorPorArticulo' => $fav['idColorPorArticulo'],
                'saved' => true,
                'message' => 'Ya estaba guardado'
            );
        } catch (Exception $ex) {
            file_put_contents('/tmp/error_favoritos.log', date('c') . ' - ' . $ex->getMessage() . PHP_EOL, FILE_APPEND);

            $response[] = array(
                'idArticulo' => isset($fav['idArticulo']) ? $fav['idArticulo'] : null,
                'idColorPorArticulo' => isset($fav['idColorPorArticulo']) ? $fav['idColorPorArticulo'] : null,
                'saved' => false,
                'message' => $ex->getMessage()
            );
        }
    }

    echo json_encode(array(
        'status' => 200,
        'message' => 'success',
        'data' => $response
    ));
    exit;

} else {
    echo json_encode(array(
        'status' => 403,
        'message' => 'Permiso denegado o usuario no logueado',
        'data' => array()
    ));
    exit;
}
?>
