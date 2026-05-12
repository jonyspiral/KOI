<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/calidad/garantias/editar/')) { ?>
<?php

$idGarantia = Funciones::post('idGarantia');
$idSucursal = Funciones::post('idSucursal');
$observaciones = Funciones::post('observaciones');

try {
	if (!$idGarantia || !$idSucursal) {
		throw new FactoryExceptionCustomException('No se recibió correctamente el ID de la garantía o la sucursal a devolver los pares');
	}

	/** @var Garantia $garantia */
	$garantia = Factory::getInstance()->getGarantia($idGarantia);
	$sucursal = Factory::getInstance()->getSucursal($garantia->idCliente, $idSucursal);

	if ($garantia->anulado()) {
		throw new FactoryExceptionCustomException('La garantía está anulada. Por favor recargue la página');
	}
	if ($garantia->clasificada == 'S') {
		throw new FactoryExceptionCustomException('La garantía fue clasificada y por eso no puede devolverse. Por favor recargue la página');
	}

	$devolucion = $garantia->devolver($sucursal, $observaciones);
	$devolucion->notificar('comercial/calidad/garantias/devolver/');

	Html::jsonSuccess('Se generó correctamente la devolución a cliente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar la devolución a cliente');
}

?>
<?php } ?>