<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/cobro_cheques_ventanilla/reimpresion_cobro_cheques_ventanilla/buscar/')) { ?>
<?php

$numero = Funciones::get('numero');
$empresa = Funciones::session('empresa');

try {
	$ventachequesCabecera = Factory::getInstance()->getCobroChequeVentanillaCabecera($numero, $empresa);
	$ventachequesCabecera->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>