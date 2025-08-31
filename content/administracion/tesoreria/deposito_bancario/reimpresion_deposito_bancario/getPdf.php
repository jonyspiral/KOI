<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/reimpresion_deposito_bancario/buscar/')) { ?>
<?php

$numero = Funciones::get('numero');
$empresa = Funciones::session('empresa');

try {
	$depositoBancarioCabecera = Factory::getInstance()->getDepositoBancarioCabecera($numero, $empresa);
	$depositoBancarioCabecera->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>