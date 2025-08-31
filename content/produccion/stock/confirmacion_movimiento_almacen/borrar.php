<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock/confirmacion_movimiento_almacen/borrar/')) { ?>
<?php

$idConfirmacion = Funciones::post('idConfirmacion');

try {
	if (!$idConfirmacion) {
		throw new FactoryExceptionCustomException('No se ingresó correctamente el parámetro "idConfirmacion"');
	}

	$movimiento = Factory::getInstance()->getMovimientoAlmacenConfirmacion($idConfirmacion);
	$movimiento->borrar()->notificar('produccion/stock/confirmacion_movimiento_almacen/borrar/');

	Html::jsonSuccess('Se rechazó correctamente el movimiento de stock de almacén');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar rechazar el movimiento de stock de almacén');
}

	?>
<?php } ?>