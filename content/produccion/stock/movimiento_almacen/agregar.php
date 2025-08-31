<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock/movimiento_almacen/agregar/')) { ?>
<?php

$idAlmacenOrigen = Funciones::post('idAlmacenOriginal');
$idArticulo = Funciones::post('idArticulo');
$idColorArticulo = Funciones::post('idColorArticulo');
$motivo = Funciones::post('motivo');
$cantidad = Funciones::post('cantidad');
$idAlmacenDestino = Funciones::post('idAlmacen');

try {
	if (!$idAlmacenDestino) {
		throw new FactoryExceptionCustomException('Deberá elegir el almacén al que quiere mover la mercadería');
	}
	if (!$motivo) {
		throw new FactoryExceptionCustomException('Deberá ingresar un motivo por el cual quiere mover la mercadería');
	}

	$movimiento = Factory::getInstance()->getMovimientoAlmacenConfirmacion();
	$movimiento->almacenOrigen = Factory::getInstance()->getAlmacen($idAlmacenOrigen);
	$movimiento->almacenDestino = Factory::getInstance()->getAlmacen($idAlmacenDestino);
	$movimiento->articulo = Factory::getInstance()->getArticulo($idArticulo);
	$movimiento->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColorArticulo);
	$movimiento->motivo = $motivo;
	for ($i = 1; $i <= 10; $i++) {
		$movimiento->cantidad[$i] = Funciones::keyIsSet($cantidad, $i, 0);
	}
	$movimiento->guardar()->notificar('produccion/stock/confirmacion_movimiento_almacen/agregar/');

	Html::jsonSuccess('Se generó correctamente el inicio del movimiento de almacén. El stock no se verá modificado hasta que este movimiento sea aprobado por quien corresponda');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar mover el stock de almacén');
}

?>
<?php } ?>