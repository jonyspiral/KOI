<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/buscar/')) { ?>
<?php

$idVentaChequesTemporal = Funciones::get('idVentaChequesTemporal');

try {
	if(empty($idVentaChequesTemporal))
		throw new FactoryExceptionCustomException('Debe especificar una venta de cheques para realizar la búsqueda');

	$depositoBancarioTemporal = Factory::getInstance()->getVentaChequesTemporal($idVentaChequesTemporal);

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