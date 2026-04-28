<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('cliente/favoritos/editar/')) { ?>
<?php

$idArticulo = isset($_POST['idArticulo']) ? $_POST['idArticulo'] : null;
$idColor = isset($_POST['idColor']) ? $_POST['idColor'] : null;
$cantidades = isset($_POST['cantidades']) ? $_POST['cantidades'] : array();
$idCliente = Usuario::logueado()->cliente->id;

error_log('[editarLibre] POST=' . print_r($_POST, true));
error_log('[editarLibre] cliente=' . var_export($idCliente, true) . ' articulo=' . var_export($idArticulo, true) . ' color=' . var_export($idColor, true));

try {
    if ($idArticulo === null || $idArticulo === '' || $idColor === null || $idColor === '') {
        throw new FactoryExceptionCustomException('Faltan datos del artículo/color para guardar cantidades.');
    }

    if (!is_array($cantidades)) {
        $cantidades = array();
    }

    try {
        $favorito = FavoritoCliente::find($idCliente, $idArticulo, $idColor);
    } catch (FactoryExceptionRegistroNoExistente $ex) {
        $favorito = FavoritoCliente::find();
        $favorito->cliente = Usuario::logueado()->cliente;
        $favorito->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColor);
        $favorito->articulo = $favorito->colorPorArticulo->articulo;
    }

    $set = array();
    for ($i = 1; $i <= 10; $i++) {
        $aux = Funciones::toInt(Funciones::keyIsSet($cantidades, $i - 1, 0));
        $aux = $aux >= 0 && $aux < 999 ? $aux : 0;

        $favorito->cantidades[$i] = $aux;
        $set[] = 'cant_' . $i . ' = ' . Datos::objectToDB($aux);
    }

    $favorito->guardar();

    $set[] = 'fecha_ultima_mod = NOW()';

    $sqlUpdate =
        'UPDATE favoritos_cliente SET ' . implode(', ', $set) .
        ' WHERE cod_cliente = ' . Datos::objectToDB($idCliente) .
        ' AND cod_articulo = ' . Datos::objectToDB($idArticulo) .
        ' AND cod_color_articulo = ' . Datos::objectToDB($idColor);

    error_log('[editarLibre] SQL=' . $sqlUpdate);

    Datos::EjecutarSQLsinQuery($sqlUpdate);

    $sqlCheck =
        'SELECT cod_cliente, cod_articulo, cod_color_articulo, ' .
        'IFNULL(cant_1,0)+IFNULL(cant_2,0)+IFNULL(cant_3,0)+IFNULL(cant_4,0)+IFNULL(cant_5,0)+' .
        'IFNULL(cant_6,0)+IFNULL(cant_7,0)+IFNULL(cant_8,0)+IFNULL(cant_9,0)+IFNULL(cant_10,0) AS total_pares ' .
        'FROM favoritos_cliente ' .
        'WHERE cod_cliente = ' . Datos::objectToDB($idCliente) .
        ' AND cod_articulo = ' . Datos::objectToDB($idArticulo) .
        ' AND cod_color_articulo = ' . Datos::objectToDB($idColor);

    $check = Datos::EjecutarSQLItem($sqlCheck);
    error_log('[editarLibre] CHECK=' . print_r($check, true));

    Html::jsonSuccess('El favorito fue modificado correctamente');
} catch (FactoryExceptionCustomException $ex) {
    error_log('[editarLibre] ERROR CUSTOM=' . $ex->getMessage());
    Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
    error_log('[editarLibre] ERROR=' . $ex->getMessage());
    Html::jsonError('Ocurrió un error al intentar modificar el favorito. ' . $ex->getMessage());
}

?>
<?php } ?>
