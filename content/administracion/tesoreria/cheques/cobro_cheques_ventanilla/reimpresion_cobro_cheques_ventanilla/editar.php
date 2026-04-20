<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/cobro_cheques_ventanilla/reimpresion_cobro_cheques_ventanilla/editar/')) { ?>
<?php

$numero = Funciones::post('numero');
$empresa = Funciones::session('empresa');
$observaciones = Funciones::post('observaciones');

try {
	if (!isset($numero))
		throw new FactoryExceptionRegistroNoExistente();
	
	$cobroChequesCabecera = Factory::getInstance()->getCobroChequeVentanillaCabecera($numero, $empresa);

	$cobroChequesCabecera->observaciones = $observaciones;

	$cobroChequesCabecera->update();
	Html::jsonSuccess('El cobro de cheques por ventanilla fue editado correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El cobro de cheques por ventanilla que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el cobro de cheques por ventanilla');
}
?>
<?php } ?>