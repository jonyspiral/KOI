<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/cobro_cheques_ventanilla/ingreso_cobro_cheques_ventanilla/borrar/')) { ?>
<?php

$idCobroCheque = Funciones::post('idCobroChequeTemporal');

try {
	if(empty($idCobroCheque))
		throw new FactoryExceptionCustomException('El cobro de cheques por ventanilla que intentó borrar no existe');

	Factory::getInstance()->beginTransaction();

	$cobroChequesTemporal = Factory::getInstance()->getCobroChequeVentanillaTemporal($idCobroCheque);
	$cobroChequesTemporal->revertirEstadoCheques();
	$cobroChequesTemporal->borrar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('El cobro de cheques por ventanilla fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El cobro de cheques por ventanilla que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el cobro de cheques por ventanilla');
}
?>
<?php } ?>