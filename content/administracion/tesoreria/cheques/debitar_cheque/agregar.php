<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/debitar_cheque/agregar/')) { ?>
<?php

function validarSiElChequeEsDebitable(Cheque $cheque){
	if(!($cheque->esPropio()))
		throw new FactoryExceptionCustomException('No puede debitar un cheque de terceros');

	if(!($cheque->esperandoEnBancoDebito()))
		throw new FactoryExceptionCustomException('No puede debitar un cheque que jamás fue entregado a un cliente');

	if($cheque->rechazado())
		throw new FactoryExceptionCustomException('No puede debitar un cheque rechazado');

	if($cheque->anulado())
		throw new FactoryExceptionCustomException('No puede debitar un cheque anulado');

	if(!$cheque->noDebitadoAcreditado())
		throw new FactoryExceptionCustomException('No puede debitar un cheque que ya fue debitado');
}

$idCheque = Funciones::post('idCheque');
$fecha = Funciones::post('fecha');
$observaciones = Funciones::post('observaciones');

try {
	$cheque = Factory::getInstance()->getCheque($idCheque);
	validarSiElChequeEsDebitable($cheque);
	$datos = array();
	$datos['observaciones'] = $observaciones;
	$datos['fecha'] = $fecha;
	$datos['usuario'] = Usuario::logueado();
	$datos['idCaja_S'] = $cheque->cuentaBancaria->caja->id;
	$datos['idCaja_E'] = $cheque->cuentaBancaria->caja->id;
	$datos['fecha_debito'] = $fecha;

	$importesSinValidarEntrada = Factory::getInstance()->getCheque()->simularArrayImportes();
	$importesSinValidarEntrada['C'][] = $cheque->simularComoImporte();

	$efectivo = Factory::getInstance()->getEfectivo();
	$efectivo->importe = $cheque->importe;
	$importesSinValidarSalida = Factory::getInstance()->getEfectivo()->simularArrayImportes();
	$importesSinValidarSalida['E'][] = $efectivo->simularComoImporte();

	$debitarCheque = Factory::getInstance()->getDebitarCheque();
	$debitarCheque->importesSinValidar['S'] = $importesSinValidarSalida;
	$debitarCheque->importesSinValidar['E'] = $importesSinValidarEntrada;
	$debitarCheque->datosSinValidar = $datos;
	$debitarCheque->empresa = Funciones::session('empresa');
	$debitarCheque->guardar();

	Html::jsonSuccess('Se acreditó correctamente el cheque Nº ' . $cheque->numero . ' (' . $cheque->banco->nombre . ')', array('idCheque' => $cheque->id));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar acreditar el cheque Nº ' . $cheque->numero . ' (' . $cheque->banco->nombre . ')');
}

?>
<?php } ?>