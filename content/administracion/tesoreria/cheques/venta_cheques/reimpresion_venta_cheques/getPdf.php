<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/venta_cheques/reimpresion_venta_cheques/buscar/')) { ?>
<?php

$numero = Funciones::get('numero');
$empresa = Funciones::session('empresa');

try {
	$ventachequesCabecera = Factory::getInstance()->getVentaChequesCabecera($numero, $empresa);
	$ventachequesCabecera->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>