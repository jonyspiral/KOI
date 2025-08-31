<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock/confirmacion_movimiento_almacen/agregar/')) { ?>
<?php

$idConfirmacion = Funciones::post('idConfirmacion');

try {
	if (!$idConfirmacion) {
		throw new FactoryExceptionCustomException('No se ingresó correctamente el parámetro "idConfirmacion"');
	}

	$movimiento = Factory::getInstance()->getMovimientoAlmacenConfirmacion($idConfirmacion);
	$movimiento->confirmar('produccion/stock/movimiento_almacen/agregar/');

	Html::jsonSuccess('Se confirmó correctamente el movimiento de stock de almacén');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar confirmar el movimiento de stock de almacén');
}

?>
<?php } ?>