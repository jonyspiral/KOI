<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/panel_de_control/agregar/')) { ?>
<?php

//Corresponde al rechazo de cheque

function validarSiElChequePuedeRechazarse(Cheque $cheque){
	if ($cheque->rechazado())
		throw new FactoryExceptionCustomException('No puede rechazar un cheque que ya fue rechazado');

	if ($cheque->anulado()) {
		throw new FactoryExceptionCustomException('No puede rechazar un cheque anulado');
	}
}

$empresa = Funciones::session('empresa');
$idCheque = Funciones::post('idCheque');
$datos['usuario'] = Usuario::logueado();

$idMotivoRechazo = Funciones::post('idMotivoRechazo');
$observaciones = Funciones::post('observaciones');

$nroNdbP = explode('-', Funciones::post('nroNdbP'));
$puntoVentaNdbP = $nroNdbP[0];
$nroNdbP = $nroNdbP[1];
$fechaNdbP = Funciones::post('fechaNdbP');
$comisionNdbP = Funciones::toFloat(Funciones::post('comisionNdbP'));
$tipoIvaNdbP = Funciones::post('tipoIvaNdbP');
$observacionesNdbP = Funciones::post('observacionesNdbP');

$comisionNdbC = Funciones::toFloat(Funciones::post('comisionNdbC'));
$observacionesNdbC = Funciones::post('observacionesNdbC');

try {
	$cheque = Factory::getInstance()->getCheque($idCheque);

	if ($cheque->empresa != $empresa){
		throw new FactoryExceptionCustomException('El cheque que intenta rechazar no existe en esta sesión');
	}

	$datos = array();
	$datos['observaciones'] = $observaciones;
	$datos['idMotivoRechazo'] = $idMotivoRechazo;
	$datos['usuario'] = Usuario::logueado();
	$datos['idCaja_S'] = $cheque->cajaActual->id;
	$datos['idCaja_E'] = Factory::getInstance()->getCaja(Cajas::chequesRechazados)->id;

	validarSiElChequePuedeRechazarse($cheque);
	$importes = $cheque->simularArrayImportes();
	$importes['C'][] = $cheque->simularComoImporte();

	$rechazoCheque = Factory::getInstance()->getRechazoCheque();
	$rechazoCheque->empresa = $empresa;
	$rechazoCheque->datosSinValidar = $datos;
	$rechazoCheque->importesSinValidar['E'] = $importes;
	$rechazoCheque->importesSinValidar['S'] = $importes;

	$ndbP = false;
	$ndbC = false;
	if ($cheque->entregadoProveedor()) {
		//NDB Al proveedor
		$ndbP = Factory::getInstance()->getNotaDeDebitoProveedor();

		//Inicia mi Magia (Si rompe comentala, xq no la pude probar =D)
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
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::chequesRechazados)->imputacion;
		$arrDetalle[] = $item;

		if ($comisionNdbP > 0){
			$item = Factory::getInstance()->getDocumentoProveedorItem();
			$item->descripcion = 'Gastos administrativos del rechazo';
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
		// Fin Magia
	}
	if ($cheque->esDeCliente()) {
		//NDB Al cliente
		$ndbC = Factory::getInstance()->getNotaDeDebito();
		$ndbC->empresa = $empresa;
		$ndbC->cliente = $cheque->cliente;
		$ndbC->letra = $ndbC->getLetra();
		$ndbC->puntoDeVenta = ($ndbC->empresa != 1 || $ndbC->letra == 'E' ? 1 : (Config::encinitas() ? Config::PUNTO_VENTA_NCNTS : 2)); //Si es cuenta 2 o NDB 'E', no es electrónica
		$ndbC->tipoDocumento = TiposDocumento::notaDeDebito;
		$ndbC->tipoDocumento2 = TiposDocumento2::ndbChequeRechazado;
		$ndbC->observaciones = $observacionesNdbC;
		$ndbC->tieneDetalle = 'S';
		$ndbC->usuario = Usuario::logueado();

		$importeNeto = $cheque->importe;

		$ndbC->importeNeto = Funciones::toFloat($importeNeto + $comisionNdbC, 2);
		//$ndbC->descuentoComercialPorc = $ndbC->cliente->creditoDescuentoEspecial;
		//$ndbC->descuentoComercialImporte = Funciones::toFloat(($ndbC->importeNeto) * $ndbC->descuentoComercialPorc / 100, 2);
		$ndbC->descuentoComercialImporte = 0;
		$ndbC->subtotal = $ndbC->importeNeto - $ndbC->descuentoComercialImporte;

		//A cuenta 2 les pongo IVA 0. A las NDB de cheque rechazado le pongo IVA sólo a la comisión, y el NETO va en NoGravado
		$ndbC->ivaPorcentaje1 = ($ndbC->empresa == 2 ? 0 : $ndbC->cliente->condicionIva->porcentajes[1]);
		$ndbC->ivaImporte1 = ($ndbC->empresa == 2 ? 0 : Funciones::toFloat($comisionNdbC * $ndbC->ivaPorcentaje1 / 100, 2));
		$ndbC->importeNoGravado = $importeNeto;

		$arrDetalle = array();

		$item = Factory::getInstance()->getDocumentoItem();
		$item->cliente = $ndbC->cliente;
		$item->descripcionItem = 'Rechazo cheque Nº ' . $cheque->numero . '. Vto: ' . $cheque->fechaVencimiento;
		$item->documentoLetra = $ndbC->letra;
		$item->documentoTipoDocumento = $ndbC->tipoDocumento;
		$item->empresa = $ndbC->empresa;
		$item->puntoDeVenta = $ndbC->puntoDeVenta;
		$item->numeroDeItem = 1;
		$item->cantidad = array();
		$item->cantidad[1] = 1;
		$item->precioUnitario = $importeNeto;
		$item->ivaPorcentaje = 0;
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::chequesRechazados)->imputacion;
		$arrDetalle[] = $item;

		$item = Factory::getInstance()->getDocumentoItem();
		$item->cliente = $ndbC->cliente;
		$item->descripcionItem = 'Comisión';
		$item->documentoLetra = $ndbC->letra;
		$item->documentoTipoDocumento = $ndbC->tipoDocumento;
		$item->empresa = $ndbC->empresa;
		$item->puntoDeVenta = $ndbC->puntoDeVenta;
		$item->numeroDeItem = 2;
		$item->cantidad = array();
		$item->cantidad[1] = 1;
		$item->precioUnitario = $comisionNdbC;
		$item->ivaPorcentaje = $ndbC->ivaPorcentaje1;
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::comisionesBancarias)->imputacion;
		$arrDetalle[] = $item;

		$ndbC->detalle = $arrDetalle;

		$ndbC->subtotal2 = $ndbC->subtotal + $ndbC->ivaImporte1;
		$ndbC->importeTotal = $ndbC->subtotal2;
		$ndbC->importePendiente = $ndbC->importeTotal;
	}

	Factory::getInstance()->beginTransaction();
	$rechazoCheque->guardar();
	if ($ndbP) {
		/** @var NotaDeDebitoProveedor $ndbP */
		$ndbP->guardar();
	}
	if ($ndbC) {
		/** @var NotaDeDebito $ndbC */
		$ndbC->guardar()->notificar('comercial/notas_de_debito/generacion/agregar/');
	}
	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se generó correctamente el rechazo del cheque');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el rechazo de cheque');
}

?>
<?php } ?>