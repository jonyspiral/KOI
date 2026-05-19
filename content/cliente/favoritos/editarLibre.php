<?php
if (!ob_get_level()) {
    ob_start();
}

function favoritosEditarLibreFatalHandler() {
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

register_shutdown_function('favoritosEditarLibreFatalHandler');

require_once('../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('cliente/favoritos/editar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
}

$idArticulo = Funciones::post('idArticulo');
$idColor = Funciones::post('idColor');
$cantidades = Funciones::post('cantidades');
$idCliente = $usuario->cliente->id;

try {
    try {
        $favorito = FavoritoCliente::find($idCliente, $idArticulo, $idColor);
    } catch (FactoryExceptionRegistroNoExistente $ex) {
        $favorito = FavoritoCliente::find();
        $favorito->cliente = $usuario->cliente;
        $favorito->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColor);
        $favorito->articulo = $favorito->colorPorArticulo->articulo;
    }

    for ($i = 1; $i <= 10; $i++) {
        $aux = Funciones::toInt($cantidades[$i - 1]);
        $favorito->cantidades[$i] = $aux >= 0 && $aux < 999 ? $aux : 0;
    }

    $favorito->guardar();

    Html::jsonSuccess('El favorito fue modificado correctamente');
} catch (Exception $ex) {
    Html::jsonError('Ocurrio un error al intentar modificar el favorito. ' . $ex->getMessage());
}