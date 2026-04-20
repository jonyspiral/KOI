<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/depositos_pendientes/agregar/')) { ?>
<?php

$numeroRecibo = Funciones::post('numeroRecibo');
$empresa = Funciones::post('empresa');
$idCliente = Funciones::post('idCliente');

try {
	$cliente = Factory::getInstance()->getCliente($idCliente);
	$recibo = Factory::getInstance()->getRecibo($numeroRecibo, $empresa);

	if ($recibo->cliente->id != ParametrosGenerales::clienteDepositosPendientes) {
		throw new FactoryExceptionCustomException('No se puede transferir un recibo que no sea del cliente Nº "' . ParametrosGenerales::clienteDepositosPendientes . '"');
	}

	Factory::getInstance()->beginTransaction();

	$recibo->cliente = $cliente;
	$recibo->update();
	$recibo->asientoContable->nombre .= ' - Trasladado a ' . $cliente->nombre;
	$recibo->asientoContable->update();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se asignó correctamente el recibo Nº "' . $recibo->numero . '" de la empresa ' . $recibo->empresa . ' al cliente Nº "' . $cliente->id . '"');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar asignar el recibo');
}
?>
<?php } ?>