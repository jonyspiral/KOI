<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/cobro_cheques_ventanilla/ingreso_cobro_cheques_ventanilla/buscar/')) { ?>
<?php

$idCobroChequeTemporal = Funciones::get('idCobroChequeTemporal');

try {
	if(empty($idCobroChequeTemporal))
		throw new FactoryExceptionCustomException('Debe especificar un cobro de cheques por ventanilla para realizar la búsqueda');

	$cobroChequesTemporal = Factory::getInstance()->getCobroChequeVentanillaTemporal($idCobroChequeTemporal);

	$cobroChequesTemporal->caja->id;
	$cobroChequesTemporal->responsable->idPersonal;
	/** @var Cheque $cheque */
	foreach($cobroChequesTemporal->cheques as $cheque)
		$cheque->expand();

	Html::jsonEncode('', $cobroChequesTemporal->expand());
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>