<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/gestion_cobranza/editar/')) { ?>
<?php

$idCliente = Funciones::post('idCliente');
$calificacion = Funciones::post('calificacion');
$observaciones = Funciones::post('observaciones');
$observacionesVendedor = Funciones::post('observacionesVendedor');

try {
	if (!isset($idCliente)) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$cliente = Factory::getInstance()->getCliente($idCliente);
	if (Usuario::logueado()->esVendedor()) {
		if (!$cliente->suVendedorEs(Usuario::logueado()->personal)) {
			throw new FactoryExceptionCustomException('El cliente que intenta editar no corresponde a su cartera de clientes');
		}
	} elseif (Usuario::logueado()->esPersonal()) {
		$cliente->observacionesGestionCobranza = $observaciones;
		$cliente->calificacion = $calificacion;
	}
	$cliente->observacionesVendedor = $observacionesVendedor;
	$cliente->guardar()->notificar('administracion/cobranzas/gestion_cobranza/editar/');

	Html::jsonSuccess('El cliente fue editado orrectamente', $cliente);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El cliente que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el cliente');
}

?>
<?php } ?>