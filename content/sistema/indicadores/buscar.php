<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/indicadores/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$indicador = Factory::getInstance()->getIndicador($id);
	Html::jsonEncode('', $indicador->expand());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>