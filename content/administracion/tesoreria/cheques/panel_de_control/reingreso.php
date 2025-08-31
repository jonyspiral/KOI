<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/panel_de_control/agregar/')) { ?>
<?php

function validarSiElChequePuedeReingresarse(Cheque $cheque) {
	if ($cheque->rechazado())
		throw new FactoryExceptionCustomException('No puede reingresar un cheque que ya fue rechazado');

	if ($cheque->anulado()) {
		throw new FactoryExceptionCustomException('No puede reingresar un cheque anulado');
	}
	if (!$cheque->concluido()) {
		throw new FactoryExceptionCustomException('No puede reingresar un cheque que aún se encuentra en cartera');
	}
	if ($cheque->esperandoEnBanco && $cheque->concluido()) {
		throw new FactoryExceptionCustomException('No puede reingresar un cheque que ya fué debitado');
	}
}

$empresa = Funciones::session('empresa');
$idCheque = Funciones::post('idCheque');
$datos['usuario'] = Usuario::logueado();

$observaciones = Funciones::post('observaciones');

$nroNdbP = explode('-', Funciones::post('nroNdbP'));
$puntoVentaNdbP = $nroNdbP[0];
$nroNdbP = $nroNdbP[1];
$fechaNdbP = Funciones::post('fechaNdbP');
$comisionNdbP = Funciones::toFloat(Funciones::post('comisionNdbP'));
$tipoIvaNdbP = Funciones::post('tipoIvaNdbP');
$observacionesNdbP = Funciones::post('observacionesNdbP');

try {
	$cheque = Factory::getInstance()->getCheque($idCheque);

	if ($cheque->empresa != $empresa) {
		throw new FactoryExceptionCustomException('El cheque que intenta reingresar no existe en esta sesión');
	}

	$datos = array();
	$datos['observaciones'] = $observaciones;
	$datos['usuario'] = Usuario::logueado();
	$datos['proveedor'] = ($cheque->entregadoProveedor() ? $cheque->proveedor : null);
	$datos['idCaja_E'] = $cheque->cajaActual->id;

	validarSiElChequePuedeReingresarse($cheque);

	$importes = $cheque->simularArrayImportes();
	$importes['C'][] = $cheque->simularComoImporte();

	$reingresoChequeCartera = Factory::getInstance()->getReingresoChequeCartera();
	$reingresoChequeCartera->empresa = $empresa;
	$reingresoChequeCartera->datosSinValidar = $datos;
	$reingresoChequeCartera->importesSinValidar['E'] = $importes;

	$ndbP = false;
	if ($cheque->entregadoProveedor()) {
		$ndbP = Factory::getInstance()->getNotaDeDebitoProveedor();

		$proveedor = null;
		try {
			$proveedor = $cheque->proveedor;
		} catch(Exception $ex) {
			throw new FactoryExceptionCustomException('No se pudo obtener el proveedor del cheque');
		}

		$ndbP->empresa = $empresa;
		$ndbP->proveedor = $proveedor;
		$ndbP->letra = $ndbP->proveedor->condicionIva->letraFactura;
		$ndbP->puntoVenta = $puntoVentaNdbP;
		$ndbP->fecha = Funciones::formatearFecha($fechaNdbP, 'd/m/Y');
		$ndbP->fechaVencimiento = (is_null($proveedor->plazoPago) ? Funciones::hoy() : Funciones::sumarTiempo($ndbP->fecha, $ndbP->proveedor->plazoPago, 'days', 'd/m/Y'));
		$ndbP->fechaPeriodoFiscal = Funciones::hoy('d/m/Y');
		$ndbP->nroDocumento = $nroNdbP;
		$ndbP->observaciones = $observacionesNdbP;
		$ndbP->documentoEnConflicto = 'N';
		$ndbP->facturaGastos = 'N';
		$ndbP->tipo = Factory::getInstance()->getTipoFactura(1);

		$arrDetalle = array();
		$arrImpuestos = array();

		$item = Factory::getInstance()->getDocumentoProveedorItem();
		$item->descripcion = 'Rechazo cheque Nº ' . $cheque->numero . '. Vto: ' . $cheque->fechaVencimiento;
		$item->nroItem = 1;
		$item->cantidad = 1;
		$item->precioUnitario = $cheque->importe;
		$item->importe = $cheque->importe;
		$item->gravado = 'N';
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::valoresADepositar)->imputacion;
		$arrDetalle[] = $item;

		if ($comisionNdbP > 0) {
			$item = Factory::getInstance()->getDocumentoProveedorItem();
			$item->descripcion = 'Gastos administrativos por devolución';
			$item->nroItem = 2;
			$item->cantidad = 1;
			$item->precioUnitario = $comisionNdbP;
			$item->importe = $comisionNdbP;
			$item->gravado = 'S';
			$item->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::comisionesBancarias)->imputacion;
			$arrDetalle[] = $item;

			$impuesto = Factory::getInstance()->getImpuesto($tipoIvaNdbP);
			$itemImpuesto = Factory::getInstance()->getImpuestoPorDocumentoProveedor();
			$itemImpuesto->impuesto = $impuesto;
			$itemImpuesto->porcentaje = ($ndbP->empresa == 2 ? 0 : $impuesto->porcentaje);
			$itemImpuesto->importe = Funciones::toFloat(($comisionNdbP * $itemImpuesto->porcentaje)/100, 2);
			$arrImpuestos[] = $itemImpuesto;

			$ndbP->impuestos = $arrImpuestos;
		}

		$ndbP->detalle = $arrDetalle;
		$ndbP->netoGravado = $comisionNdbP;
		$ndbP->netoNoGravado = $cheque->importe;
		$ndbP->importeTotal = $cheque->importe + $comisionNdbP + $itemImpuesto->importe;
	}

	Factory::getInstance()->beginTransaction();
	$reingresoChequeCartera->guardar();
	if ($ndbP) {
		/** @var NotaDeDebitoProveedor $ndbP */
		$ndbP->guardar();
	}
	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se generó correctamente el reingreso del cheque');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el reingreso de cheque');
}

?>
<?php } ?>