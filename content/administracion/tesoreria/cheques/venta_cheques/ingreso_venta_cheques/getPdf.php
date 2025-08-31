<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/buscar/')) { ?>
<?php

$idVentaChequesTemporal = Funciones::get('idVentaChequesTemporal');

try {
	$depositoBancarioTemporal = Factory::getInstance()->getVentaChequesTemporal($idVentaChequesTemporal);
	$depositoBancarioTemporal->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>