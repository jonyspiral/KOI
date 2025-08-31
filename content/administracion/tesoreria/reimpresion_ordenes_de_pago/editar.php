<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/reimpresion_ordenes_de_pago/editar/')) { ?>
<?php

$numero = Funciones::post('numero');
$empresa = Funciones::session('empresa');
$idImputacion = Funciones::post('idImputacion');
$beneficiario = Funciones::post('beneficiario');
$observaciones = Funciones::post('observaciones');

try {
	if (!isset($numero))
		throw new FactoryExceptionRegistroNoExistente();

	$ordenDePago = Factory::getInstance()->getOrdenDePago($numero, $empresa);

	if($ordenDePago->anulado == 'S')
		throw new FactoryExceptionCustomException('No puede editar una orden de pago anulada.');

	if(is_null($idImputacion))
		throw new FactoryExceptionCustomException('El campo imputación es obligatorio.');

	$ordenDePago->imputacion = Factory::getInstance()->getImputacion($idImputacion);
	$ordenDePago->beneficiario = $beneficiario;
	$ordenDePago->observaciones = $observaciones;

	$ordenDePago->update();
	Html::jsonSuccess('La orden de pago fue editada correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La orden de pago que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar la orden de pago');
}
?>
<?php } ?>