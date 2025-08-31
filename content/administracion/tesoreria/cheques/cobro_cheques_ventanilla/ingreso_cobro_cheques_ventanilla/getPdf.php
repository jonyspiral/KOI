<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/cobro_cheques_ventanilla/ingreso_cobro_cheques_ventanilla/buscar/')) { ?>
<?php

$idCobroChequeTemporal = Funciones::get('idCobroChequeTemporal');

try {
	$cobroChequesTemporal = Factory::getInstance()->getCobroChequeVentanillaTemporal($idCobroChequeTemporal);
	$cobroChequesTemporal->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>