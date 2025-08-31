<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/borrar/')) { ?>
<?php

$idVentaCheques = Funciones::post('idVentaCheques');

try {
	if(empty($idVentaCheques))
		throw new FactoryExceptionCustomException('La venta de cheques que intentó borrar no existe');

	Factory::getInstance()->beginTransaction();

	$ventaChequesTemporal = Factory::getInstance()->getVentaChequesTemporal($idVentaCheques);
	$ventaChequesTemporal->revertirEstadoCheques();
	$ventaChequesTemporal->borrar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('La venta de cheques fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La venta de cheques que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la venta de cheques');
}
?>
<?php } ?>