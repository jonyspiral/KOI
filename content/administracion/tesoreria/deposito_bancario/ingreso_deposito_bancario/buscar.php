<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/ingreso_deposito_bancario/buscar/')) { ?>
<?php

$idDepositoBancarioTemporal = Funciones::get('idDepositoBancarioTemporal');
try {
	if(empty($idDepositoBancarioTemporal))
		throw new FactoryExceptionCustomException('Debe especificar un depósito bancario para realizar la búsqueda');

	$depositoBancarioTemporal = Factory::getInstance()->getDepositoBancarioTemporal($idDepositoBancarioTemporal);

	$depositoBancarioTemporal->caja->id;
	$depositoBancarioTemporal->cuentaBancaria->id;
	/** @var Cheque $cheque */
	foreach($depositoBancarioTemporal->cheques as $cheque)
		$cheque->expand();

	Html::jsonEncode('', $depositoBancarioTemporal->expand());
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>