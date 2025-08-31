<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/ingreso_deposito_bancario/buscar/')) { ?>
<?php

$idDepositoBancarioTemporal = Funciones::get('idDepositoBancarioTemporal');

try {
	$depositoBancarioTemporal = Factory::getInstance()->getDepositoBancarioTemporal($idDepositoBancarioTemporal);
	$depositoBancarioTemporal->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>