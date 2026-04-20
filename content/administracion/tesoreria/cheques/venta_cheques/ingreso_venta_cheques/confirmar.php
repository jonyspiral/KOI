<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/agregar/')) { ?>
<?php

$idVentaChequesTemporal = Funciones::post('idVentaChequesTemporal');
$numeroTransaccion = Funciones::post('numeroTransaccion');
$observaciones = Funciones::post('observaciones');

try {
	Factory::getInstance()->beginTransaction();

	$ventaChequesTemporal = Factory::getInstance()->getVentaChequesTemporal($idVentaChequesTemporal);

	$datos = array();
	$datos['fecha'] = $ventaChequesTemporal->fecha;
	$datos['observaciones'] = $observaciones;
	$datos['idCuentaBancaria'] = $ventaChequesTemporal->cuentaBancaria->id;
	$datos['idProveedor'] = $ventaChequesTemporal->cuentaBancaria->proveedor->id;
	$datos['usuario'] = Usuario::logueado();
	$datos['idCaja_S'] = $ventaChequesTemporal->caja->id;
	$datos['idCaja_E'] = $ventaChequesTemporal->cuentaBancaria->caja->id;

	$importesSinValidarSalida = Factory::getInstance()->getCheque()->simularArrayImportes();
	$importeEfectivo = 0;
	foreach($ventaChequesTemporal->cheques as $cheque){
		/** @var Cheque $cheque */
		$importesSinValidarSalida['C'][] = $cheque->simularComoImporte();
		$importeEfectivo += $cheque->importe;
	}

	$efectivo = Factory::getInstance()->getEfectivo();
	$efectivo->importe = $importeEfectivo;
	$importesSinValidarEntrada = Factory::getInstance()->getEfectivo()->simularArrayImportes();
	$importesSinValidarEntrada['E'][] = $efectivo->simularComoImporte();

	$ventaCheques = Factory::getInstance()->getVentaCheques();
	$ventaCheques->importesSinValidar['S'] = $importesSinValidarSalida;
	$ventaCheques->importesSinValidar['E'] = $importesSinValidarEntrada;
	$ventaCheques->datosSinValidar = $datos;
	$ventaCheques->empresa = $ventaChequesTemporal->empresa;
	$ventaCheques->guardar();

	$ventaChequesTemporal->confirmar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se confirmó correctamente la venta de cheques');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar confirmar la venta de cheques');
}

?>
<?php } ?>