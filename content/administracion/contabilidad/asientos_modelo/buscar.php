<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/asientos_modelo/buscar/')) { ?>
<?php

try {
	$where .= 'anulado = ' . Datos::objectToDB('N');
	$asientos = Factory::getInstance()->getListObject('AsientoContableModelo', 'anulado = ' . Datos::objectToDB('N') . ' ORDER BY cod_asiento_modelo DESC');
	foreach($asientos as $asiento) {
		$asiento->expand();
	}
	Html::jsonEncode('', $asientos);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>