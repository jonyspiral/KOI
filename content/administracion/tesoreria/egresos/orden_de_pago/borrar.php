<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/egresos/orden_de_pago/borrar/')) { ?>
<?php

$idOp = Funciones::post('idOrdenDePago');
$empresa = Funciones::session('empresa');

try {
	$op = Factory::getInstance()->getOrdenDePago($idOp, $empresa);

	if ($op->importePendiente != $op->importeTotal)
	throw new FactoryExceptionCustomException('No se puede borrar una órden de pago ya aplicada');

	$op->borrar();

	Html::jsonSuccess('Se borró correctamente la orden de pago');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la orden de pago');
}

?>
<?php } ?>