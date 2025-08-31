<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/nota_de_debito/agregar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::post('puntoDeVenta');
$letra = Funciones::post('letra');
$nro = Funciones::post('numero');
$idCausa = Funciones::post('idCausa');

try {
	Factory::getInstance()->beginTransaction();

	$ndb = Factory::getInstance()->getNotaDeDebito($empresa, $puntoDeVenta, TiposDocumento::notaDeDebito, $nro, $letra);
	$ncr = Factory::getInstance()->getNotaDeCredito();
	$ncr->empresa = $empresa;
	$ncr->tipoDocumento2 = TiposDocumento2::ncrNotaDeDebito;
	$ncr->cliente = $ndb->cliente;
	$ncr->causa = Factory::getInstance()->getCausaNotaDeCredito($idCausa);
	$ncr->observaciones = 'Nota de crédito para la nota de débito Nº ' . $ndb->numeroComprobante . '. || ' . $ndb->observaciones;
	$ncr->tieneDetalle = 'S';

	$ncr->descuentoComercialPorc = $ndb->descuentoComercialPorc;
	$ncr->descuentoDespachoImporte = $ndb->descuentoDespachoImporte;
	$ncr->documentoCancelatorio = $ndb;

	$ncr->importeNeto = $ndb->importeNeto;
	$ncr->importeNoGravado = $ndb->importeNoGravado;
	$ncr->ivaImporte1 = $ndb->ivaImporte1;
	$ncr->ivaImporte2 = $ndb->ivaImporte2;
	$ncr->ivaImporte3 = $ndb->ivaImporte3;
	$ncr->ivaPorcentaje1 = $ndb->ivaPorcentaje1;
	$ncr->ivaPorcentaje2 = $ndb->ivaPorcentaje2;
	$ncr->ivaPorcentaje3 = $ndb->ivaPorcentaje3;

	$arrDetalle = array();
	$nroItem = 1;
	foreach ($ndb->detalleItems as $ndbItem) {
		$item = Factory::getInstance()->getDocumentoItem();
		$item->cantidad = $ndbItem->cantidad;
		$item->descuentoPedido = $ndbItem->descuentoPedido;
		$item->ivaPorcentaje = $ndbItem->ivaPorcentaje;
		$item->numeroDeItem = $nroItem;
		$item->precioUnitario = $ndbItem->precioUnitario;
		$item->precioUnitarioFinal = $ndbItem->precioUnitarioFinal;
		$item->recargoPedido = $ndbItem->recargoPedido;
		if ($ndb->tieneDetalle()) {
			$item->descripcionItem = $ndbItem->descripcionItem;
		} else {
			$item->almacen = $ndbItem->almacen;
			$item->articulo = $ndbItem->articulo;
			$item->colorPorArticulo = $ndbItem->colorPorArticulo;
		}
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ingresosPorVentas)->imputacion;
		$arrDetalle[] = $item;
		$nroItem++;
	}
	$ncr->detalle =  $arrDetalle;
	$ncr->guardar()->notificar('comercial/notas_de_credito/generacion/nota_de_debito/agregar/');

	$arr['puntoDeVenta'] = $ndb->puntoDeVenta;
	$arr['nro'] = $ndb->numero;
	$arr['letra'] = $ndb->letra;

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