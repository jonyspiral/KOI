<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/seguimiento_clientes/agregar/')) { ?>
<?php

$idCliente = Funciones::post('idCliente');
$fecha = Funciones::post('fecha');
$observaciones = Funciones::post('observaciones');

try {
	if(empty($idCliente) || empty($observaciones))
		throw new FactoryExceptionCustomException('Todos los campos son obligatorios');

	$gestionClientesCobranza = Factory::getInstance()->getSeguimientoCliente();

	$gestionClientesCobranza->cliente = Factory::getInstance()->getCliente($idCliente);
	$gestionClientesCobranza->fechaGestion = Funciones::hoy();
	$gestionClientesCobranza->observaciones = $observaciones;
	$gestionClientesCobranza->estado = '0';

	$gestionClientesCobranza->guardar();

	Html::jsonSuccess('La gestión se agregó correctamente', $gestionClientesCobranza->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar agregar la gestión');
}

?>
<?php } ?>