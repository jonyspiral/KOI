<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock_mp/movimiento_almacen/agregar/')) { ?>
<?php

$idAlmacen = Funciones::post('idAlmacen');
$idMaterial = Funciones::post('idMaterial');
$idColor = Funciones::post('idColor');
$cantidad = Funciones::post('cantidad');

try {
    $consumo = Factory::getInstance()->getConsumoStockMP();
    $consumo->almacen = Factory::getInstance()->getAlmacen($idAlmacen);
    $consumo->material = Factory::getInstance()->getMaterial($idMaterial);
    $consumo->colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($idMaterial, $idColor);
    for ($i = 1; $i <= 10; $i++) {
        $consumo->cantidad[$i] = Funciones::keyIsSet($cantidad, $i, 0);
    }
    $consumo->guardar()->notificar('produccion/stock_mp/consumos/agregar/');

    Html::jsonSuccess('Se guardó correctamente el consumo de stock');
} catch (FactoryExceptionCustomException $ex) {
    Html::jsonError($ex->getMessage());
} catch (Exception $ex){
    Html::jsonError('Ocurrió un error al intentar guardar el consumo de stock');
}

?>
<?php } ?>