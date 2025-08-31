<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/agregar/')) { ?>
<?php

$idCajaOrigen = Funciones::post('idCajaOrigen');
$idCuentaBancaria = Funciones::post('idCuentaBancaria');
$fecha = Funciones::post('fecha');
$arrayCheques = Funciones::post('cheques');

try {
	Factory::getInstance()->beginTransaction();

	$ventaChequesTemporal = Factory::getInstance()->getVentaChequesTemporal();

	foreach($arrayCheques as $itemCheque){
		$cheque = Factory::getInstance()->getCheque($itemCheque['id']);
		$ventaChequesTemporal->addCheque($cheque);
	}

	$ventaChequesTemporal->caja = Factory::getInstance()->getCaja($idCajaOrigen);
	$ventaChequesTemporal->cuentaBancaria = Factory::getInstance()->getCuentaBancaria($idCuentaBancaria);
	$ventaChequesTemporal->fecha = $fecha;
	$ventaChequesTemporal->empresa = Funciones::session('empresa');

	$ventaChequesTemporal->guardar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se generó correctamente la venta de cheques y se encuentra disponible para confirmar.');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar la venta de cheques');
}

?>
<?php } ?>