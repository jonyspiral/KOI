<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/asientos_contables/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$asientoModelo = Factory::getInstance()->getAsientoContableModelo($id);
	Html::jsonEncode('', $asientoModelo->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>