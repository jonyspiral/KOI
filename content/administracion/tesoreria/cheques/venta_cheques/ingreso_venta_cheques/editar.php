<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/editar/')) { ?>
<?php

$idVentaChequesTemporal = Funciones::post('idVentaChequesTemporal');
$idCuentaBancaria = Funciones::post('idCuentaBancaria');
$fecha = Funciones::post('fecha');
$arrayCheques = Funciones::post('cheques');

try {
	Factory::getInstance()->beginTransaction();

	$depositoBancarioTemporal = Factory::getInstance()->getVentaChequesTemporal($idVentaChequesTemporal);

	$cheques = array();
	foreach($arrayCheques as $chequeItem){
		$cheque = Factory::getInstance()->getCheque($chequeItem['id']);
		$cheques[] = $cheque;
	}

	$depositoBancarioTemporal->chequesNuevos = $cheques;
	$depositoBancarioTemporal->cuentaBancaria = Factory::getInstance()->getCuentaBancaria($idCuentaBancaria);
	$depositoBancarioTemporal->fecha = $fecha;

	$depositoBancarioTemporal->guardar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se editó correctamente la venta de cheques y se encuentra disponible para confirmar.');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar la venta de cheques');
}

?>
<?php } ?>