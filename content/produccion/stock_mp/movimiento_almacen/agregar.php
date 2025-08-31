<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock_mp/movimiento_almacen/agregar/')) { ?>
<?php

$idAlmacenOrigen = Funciones::post('idAlmacenOriginal');
$idMaterial = Funciones::post('idMaterial');
$idColor = Funciones::post('idColor');
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

	$movimiento = Factory::getInstance()->getMovimientoAlmacenConfirmacionMP();
	$movimiento->almacenOrigen = Factory::getInstance()->getAlmacen($idAlmacenOrigen);
	$movimiento->almacenDestino = Factory::getInstance()->getAlmacen($idAlmacenDestino);
	$movimiento->material = Factory::getInstance()->getMaterial($idMaterial);
	$movimiento->colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($idMaterial, $idColor);
	$movimiento->motivo = $motivo;
	for ($i = 1; $i <= 10; $i++) {
		$movimiento->cantidad[$i] = Funciones::keyIsSet($cantidad, $i, 0);
	}
	$movimiento->guardar()->notificar('produccion/stock_mp/confirmacion_movimiento_almacen/agregar/');

	Html::jsonSuccess('Se generó correctamente el inicio del movimiento de almacén. El stock no se verá modificado hasta que este movimiento sea aprobado por quien corresponda');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar mover el stock de almacén');
}

?>
<?php } ?>