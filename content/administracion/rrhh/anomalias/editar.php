<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/rrhh/anomalias/editar/')) { ?>
<?php

$idCliente = Funciones::post('idCliente');
$calificacion = Funciones::post('calificacion');
$observaciones = Funciones::post('observaciones');

try {
	if (!isset($idCliente)) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$cliente = Factory::getInstance()->getCliente($idCliente);
	$cliente->observacionesGestionCobranza = $observaciones;
	$cliente->calificacion = $calificacion;
	$cliente->guardar()->notificar('administracion/cobranzas/gestion_cobranza/editar/');

	Html::jsonSuccess('El cliente fue editado orrectamente', $cliente);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El cliente que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el cliente');
}

?>
<?php } ?>