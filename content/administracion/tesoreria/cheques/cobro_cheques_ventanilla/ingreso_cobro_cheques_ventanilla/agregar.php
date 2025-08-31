<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/cobro_cheques_ventanilla/ingreso_cobro_cheques_ventanilla/agregar/')) { ?>
<?php

$idCajaOrigen = Funciones::post('idCajaOrigen');
$idPersonal = Funciones::post('idResponsable');
$fecha = Funciones::post('fecha');
$arrayCheques = Funciones::post('cheques');

try {
	Factory::getInstance()->beginTransaction();

	$cobroChequesTemporal = Factory::getInstance()->getCobroChequeVentanillaTemporal();

	foreach($arrayCheques as $itemCheque){
		$cheque = Factory::getInstance()->getCheque($itemCheque['id']);
		$cobroChequesTemporal->addCheque($cheque);
	}

	$cobroChequesTemporal->caja = Factory::getInstance()->getCaja($idCajaOrigen);
	$cobroChequesTemporal->responsable = Factory::getInstance()->getPersonal($idPersonal);
	$cobroChequesTemporal->fecha = $fecha;

	$cobroChequesTemporal->guardar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se generó correctamente el cobro de cheques por ventanilla y se encuentra disponible para confirmar.');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el cobro de cheques por ventanilla');
}

?>
<?php } ?>