<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/periodos_fiscales/tipos/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$tipoPeriodoFiscal = Factory::getInstance()->getTipoPeriodoFiscal($id);
	Html::jsonEncode('', $tipoPeriodoFiscal->expand());
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>