<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/factura/agregar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$letra = Funciones::get('letra');
$nro = Funciones::get('numero'); 
$idCausa = Funciones::get('idCausa'); 

try {
	Factory::getInstance()->beginTransaction();

	$fac = Factory::getInstance()->getFactura($empresa, $puntoDeVenta, TiposDocumento::factura, $nro, $letra);
	$ncr = Factory::getInstance()->getNotaDeCredito();
	$ncr->empresa = $empresa;
	$ncr->tipoDocumento2 = TiposDocumento2::ncrFactura;
	$ncr->cliente = $fac->cliente;
	$ncr->causa = Factory::getInstance()->getCausaNotaDeCredito($idCausa);
	$ncr->observaciones = 'Nota de crédito para la factura Nº ' . $fac->numeroComprobante . '. || ' . $fac->observaciones;
	$ncr->tieneDetalle = 'N';

	$ncr->descuentoComercialPorc = $fac->descuentoComercialPorc;
	$ncr->descuentoComercialImporte = $fac->descuentoComercialImporte;
	$ncr->descuentoDespachoImporte = $fac->descuentoDespachoImporte;
	$ncr->documentoCancelatorio = $fac;

	$ncr->importeNeto = $fac->importeNeto;
	$ncr->importeNoGravado = $fac->importeNoGravado;
	$ncr->ivaImporte1 = $fac->ivaImporte1;
	$ncr->ivaImporte2 = $fac->ivaImporte2;
	$ncr->ivaImporte3 = $fac->ivaImporte3;
	$ncr->ivaPorcentaje1 = $fac->ivaPorcentaje1;
	$ncr->ivaPorcentaje2 = $fac->ivaPorcentaje2;
	$ncr->ivaPorcentaje3 = $fac->ivaPorcentaje3;

	$arrDetalle = array();
	$nroItem = 1;
	foreach ($fac->detalleItems as $facItem) {
		$item = Factory::getInstance()->getDocumentoItem();
		$item->cantidad = $facItem->cantidad;
		$item->descuentoPedido = $facItem->descuentoPedido;
		$item->ivaPorcentaje = $facItem->ivaPorcentaje;
		$item->numeroDeItem = $nroItem;
		$item->precioUnitario = $facItem->precioUnitario;
		$item->precioUnitarioFinal = $facItem->precioUnitarioFinal;
		$item->recargoPedido = $facItem->recargoPedido;
		if ($fac->tieneDetalle()) {
			$item->descripcionItem = $facItem->descripcionItem;
		} else {
			$item->almacen = $facItem->almacen;
			$item->articulo = $facItem->articulo;
			$item->colorPorArticulo = $facItem->colorPorArticulo;
		}
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ingresosPorVentas)->imputacion;
		$arrDetalle[] = $item;
		$nroItem++;
	}
	$ncr->detalle =  $arrDetalle;
	$ncr->guardar()->notificar('comercial/notas_de_credito/generacion/factura/agregar/');

	$arr['puntoDeVenta'] = $fac->puntoDeVenta;
	$arr['nro'] = $fac->numero;
	$arr['letra'] = $fac->letra;

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se generó correctamente la nota de crédito', $arr);
} catch (FactoryExceptionCustomException $ex) {
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar generar la(s) nota(s) de crédito');
}

?>
<?php } ?>