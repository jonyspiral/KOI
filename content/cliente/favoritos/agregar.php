<?php
if (!ob_get_level()) {
    ob_start();
}

function favoritosAgregarFatalHandler() {
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
}

register_shutdown_function('favoritosAgregarFatalHandler');

require_once('../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('cliente/favoritos/agregar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
}

$idArticulo = Funciones::post('idArticulo');
$idColor = Funciones::post('idColor');

try {
    $favorito = FavoritoCliente::find();
    $favorito->cliente = $usuario->cliente;
    $favorito->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColor);
    $favorito->articulo = $favorito->colorPorArticulo->articulo;

    $favorito->guardar();

    Html::jsonSuccess('El articulo fue anadido a favoritos');
} catch (FactoryExceptionRegistroExistente $ex) {
    Html::jsonSuccess('El articulo ya estaba marcado como favorito');
} catch (Exception $ex) {
    Html::jsonError($ex->getMessage());
}