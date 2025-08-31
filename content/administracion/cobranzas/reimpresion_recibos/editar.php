<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/reimpresion_recibos/editar/')) { ?>
<?php

$numero = Funciones::post('numero');
$empresa = Funciones::session('empresa');
$idImputacion = Funciones::post('idImputacion');
$recibidoDe = Funciones::post('recibidoDe');
$observaciones = Funciones::post('observaciones');

try {
	if (!isset($numero))
		throw new FactoryExceptionRegistroNoExistente();
	
	$recibo = Factory::getInstance()->getRecibo($numero, $empresa);

	if(is_null($idImputacion))
		throw new FactoryExceptionCustomException('El campo imputación es obligatorio.');

	$recibo->imputacion = Factory::getInstance()->getImputacion($idImputacion);
	$recibo->recibidoDe = $recibidoDe;
	$recibo->observaciones = $observaciones;

	$recibo->update();
	Html::jsonSuccess('El recibo fue editado correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El recibo que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el recibo');
}
?>
<?php } ?>