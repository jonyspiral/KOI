<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_debito/generacion/nota_de_credito/agregar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::post('puntoDeVenta');
$letra = Funciones::post('letra');
$nro = Funciones::post('numero');

try {
	Factory::getInstance()->beginTransaction();

	$ncr = Factory::getInstance()->getNotaDeCredito($empresa, $puntoDeVenta, TiposDocumento::notaDeCredito, $nro, $letra);
	$ndb = Factory::getInstance()->getNotaDeDebito();
	$ndb->empresa = $empresa;
	$ndb->tipoDocumento2 = TiposDocumento2::ndbNotaDeCredito;
	$ndb->cliente = $ncr->cliente;
	$ndb->observaciones = 'Nota de débito para la nota de crédito Nº ' . $ncr->numeroComprobante . '. || ' . $ncr->observaciones;
	$ndb->tieneDetalle = 'S';

	$ndb->descuentoComercialPorc = $ncr->descuentoComercialPorc;
	$ndb->descuentoComercialImporte = $ncr->descuentoComercialImporte;
	$ndb->descuentoDespachoImporte = $ncr->descuentoDespachoImporte;

	$ndb->importeNeto = $ncr->importeNeto;
	$ndb->importeNoGravado = $ncr->importeNoGravado;
	$ndb->ivaImporte1 = $ncr->ivaImporte1;
	$ndb->ivaImporte2 = $ncr->ivaImporte2;
	$ndb->ivaImporte3 = $ncr->ivaImporte3;
	$ndb->ivaPorcentaje1 = $ncr->ivaPorcentaje1;
	$ndb->ivaPorcentaje2 = $ncr->ivaPorcentaje2;
	$ndb->ivaPorcentaje3 = $ncr->ivaPorcentaje3;

	$arrDetalle = array();
	$nroItem = 1;
	foreach ($ncr->detalleItems as $ncrItem) {
		$item = Factory::getInstance()->getDocumentoItem();
		$item->cantidad = $ncrItem->cantidad;
		$item->descuentoPedido = $ncrItem->descuentoPedido;
		$item->ivaPorcentaje = $ncrItem->ivaPorcentaje;
		$item->numeroDeItem = $nroItem;
		$item->precioUnitario = $ncrItem->precioUnitario;
		$item->precioUnitarioFinal = $ncrItem->precioUnitarioFinal;
		$item->recargoPedido = $ncrItem->recargoPedido;
		$item->descripcionItem = $ncrItem->descripcionItem;
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::deudoresPorVentas)->imputacion;
		$arrDetalle[] = $item;
		$nroItem++;
	}
	$ndb->detalle =  $arrDetalle;
	$ndb->guardar()->notificar('comercial/notas_de_debito/generacion/nota_de_credito/agregar/');

	$arr['puntoDeVenta'] = $ncr->puntoDeVenta;
	$arr['nro'] = $ncr->numero;
	$arr['letra'] = $ncr->letra;

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se generó correctamente la nota de débito', $arr);
} catch (FactoryExceptionCustomException $ex) {
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar generar la(s) nota(s) de débito');
}

?>
<?php } ?>