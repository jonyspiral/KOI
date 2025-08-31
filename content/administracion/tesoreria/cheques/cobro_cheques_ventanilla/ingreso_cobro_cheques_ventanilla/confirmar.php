<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/cobro_cheques_ventanilla/ingreso_cobro_cheques_ventanilla/agregar/')) { ?>
<?php

$idVentaChequesTemporal = Funciones::post('idCobroChequeTemporal');
$observaciones = Funciones::post('observaciones');

try {
	if(empty($idVentaChequesTemporal)){
		throw new FactoryExceptionCustomException('El cobro de cheques por ventanilla que se quiere confirmar no existe');
	}

	Factory::getInstance()->beginTransaction();

	$ventaChequesTemporal = Factory::getInstance()->getCobroChequeVentanillaTemporal($idVentaChequesTemporal);

	$datos = array();
	$datos['fecha'] = $ventaChequesTemporal->fecha;
	$datos['observaciones'] = $observaciones;
	$datos['idResponsable'] = $ventaChequesTemporal->responsable->idPersonal;
	$datos['usuario'] = Usuario::logueado();
	$datos['idCaja_S'] = $ventaChequesTemporal->caja->id;
	$datos['idCaja_E'] = $ventaChequesTemporal->caja->id;

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

	$cobroCheques = Factory::getInstance()->getCobroChequeVentanilla();
	$cobroCheques->importesSinValidar['S'] = $importesSinValidarSalida;
	$cobroCheques->importesSinValidar['E'] = $importesSinValidarEntrada;
	$cobroCheques->datosSinValidar = $datos;
	$cobroCheques->empresa = Funciones::session('empresa');
	$cobroCheques->guardar();

	$ventaChequesTemporal->confirmar();

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se confirmó correctamente el cobro de cheques por ventanilla');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar confirmar el cobro de cheques por ventanilla');
}

?>
<?php } ?>