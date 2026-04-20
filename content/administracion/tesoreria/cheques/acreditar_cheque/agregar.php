<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/acreditar_cheque/agregar/')) { ?>
<?php

function validarSiElChequeEsAcreditable(Cheque $cheque){
	if(!($cheque->esperandoEnBancoCredito()))
		throw new FactoryExceptionCustomException('No puede acreditar un cheque que jamás fue depositado');

	if($cheque->rechazado())
		throw new FactoryExceptionCustomException('No puede acreditar un cheque rechazado');

	if($cheque->anulado())
		throw new FactoryExceptionCustomException('No puede acreditar un cheque anulado');
}

$idCheque = Funciones::post('idCheque');
$fecha = Funciones::post('fecha');
$observaciones = Funciones::post('observaciones');

try {
	$cheque = Factory::getInstance()->getCheque($idCheque);
	validarSiElChequeEsAcreditable($cheque);
	$datos = array();
	$datos['observaciones'] = $observaciones;
	$datos['fecha'] = $fecha;
	$datos['usuario'] = Usuario::logueado();
	$datos['idCaja_S'] = $cheque->cajaActual->id;
	$datos['idCaja_E'] = $cheque->cajaActual->id;
	$datos['fecha_credito'] = $fecha;

	$importesSinValidarSalida = Factory::getInstance()->getCheque()->simularArrayImportes();
	$importesSinValidarSalida['C'][] = $cheque->simularComoImporte();

	$efectivo = Factory::getInstance()->getEfectivo();
	$efectivo->importe = $cheque->importe;
	$importesSinValidarEntrada = Factory::getInstance()->getEfectivo()->simularArrayImportes();
	$importesSinValidarEntrada['E'][] = $efectivo->simularComoImporte();

	$acreditarCheque = Factory::getInstance()->getAcreditarCheque();
	$acreditarCheque->importesSinValidar['S'] = $importesSinValidarSalida;
	$acreditarCheque->importesSinValidar['E'] = $importesSinValidarEntrada;
	$acreditarCheque->datosSinValidar = $datos;
	$acreditarCheque->empresa = Funciones::session('empresa');
	$acreditarCheque->guardar();

	Html::jsonSuccess('Se acreditó correctamente el cheque Nº ' . $cheque->numero . ' (' . $cheque->banco->nombre . ')', array('idCheque' => $cheque->id));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar acreditar el cheque Nº ' . $cheque->numero . ' (' . $cheque->banco->nombre . ')');
}

?>
<?php } ?>