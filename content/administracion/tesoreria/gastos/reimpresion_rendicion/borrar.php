<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/reimpresion_rendicion/borrar/')) { ?>
<?php

$numeroRendicion = Funciones::post('numero');
$empresa = Funciones::session('empresa');

try {
	$rendicion = Factory::getInstance()->getRendicionGastos($numeroRendicion, $empresa);

	if($rendicion->importePendiente != $rendicion->importeTotal)
		throw new FactoryExceptionCustomException('No puede borrar un documento que ya fue aplicado');

	$rendicion->borrar();

	Html::jsonSuccess('Se borró correctamente la rendición de gastos');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la rendición de gastos');
}

?>
<?php } ?>