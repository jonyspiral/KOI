<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/cobro_cheques_ventanilla/ingreso_cobro_cheques_ventanilla/editar/')) { ?>
<?php

$idCobroChequesTemporal = Funciones::post('idCobroChequeTemporal');
$idResponsable = Funciones::post('idResponsable');
$fecha = Funciones::post('fecha');
$arrayCheques = Funciones::post('cheques');

try {
	if (!isset($idCobroChequesTemporal))
		throw new FactoryExceptionRegistroNoExistente();

	Factory::getInstance()->beginTransaction();

	$cobroChequesTemporal = Factory::getInstance()->getCobroChequeVentanillaTemporal($idCobroChequesTemporal);

	$cheques = array();
	foreach($arrayCheques as $chequeItem){
		$cheque = Factory::getInstance()->getCheque($chequeItem['id']);
		$cheques[] = $cheque;
	}

	$cobroChequesTemporal->chequesNuevos = $cheques;
	$cobroChequesTemporal->responsable = Factory::getInstance()->getPersonal($idResponsable);
	$cobroChequesTemporal->fecha = $fecha;

	$cobroChequesTemporal->guardar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se editó correctamente el cobro de cheques por ventanilla y se encuentra disponible para confirmar.');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El cobro de cheques por ventanilla que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el cobro de cheques por ventanilla');
}

?>
<?php } ?>