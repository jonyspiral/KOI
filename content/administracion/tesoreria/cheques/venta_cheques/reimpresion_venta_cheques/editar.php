<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/venta_cheques/reimpresion_venta_cheques/editar/')) { ?>
<?php

$numero = Funciones::post('numero');
$empresa = Funciones::session('empresa');
$observaciones = Funciones::post('observaciones');

try {
	if (!isset($numero))
		throw new FactoryExceptionRegistroNoExistente();
	
	$ventaChequesCabecera = Factory::getInstance()->getVentaChequesCabecera($numero, $empresa);

	$ventaChequesCabecera->observaciones = $observaciones;

	$ventaChequesCabecera->update();
	Html::jsonSuccess('La venta de cheques fue editada correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La venta de cheques que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar la venta de cheques');
}
?>
<?php } ?>