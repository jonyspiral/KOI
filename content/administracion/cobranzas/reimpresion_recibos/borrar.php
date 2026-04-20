<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/reimpresion_recibos/borrar/')) { ?>
<?php

$idRecibo = Funciones::post('numero');
$empresa = Funciones::session('empresa');

try {
	$rec = Factory::getInstance()->getRecibo($idRecibo, $empresa);

	if ($rec->importePendiente != $rec->importeTotal)
		throw new FactoryExceptionCustomException('No se puede borrar un recibo ya aplicado');

	$rec->borrar();

	Html::jsonSuccess('Se borró correctamente el recibo');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el recibo');
}

?>
<?php } ?>