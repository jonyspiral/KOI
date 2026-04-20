<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_debito/generacion/generica/agregar/')) { ?>
<?php

$idCliente = Funciones::post('idCliente');
$tipoDocumento2 = Funciones::post('tipoDocumento2');
$detalleItem = Funciones::post('detalleItem');
$detalleItemNoGravado = Funciones::post('detalleItemNoGravado');
$observaciones = Funciones::post('observaciones');
$importeNoGravado = Funciones::post('importeNoGravado');
$importeGravado = Funciones::post('importeGravado');
$aplicarDescuento = Funciones::post('aplicarDescuento') == 'S';

try {
	Factory::getInstance()->beginTransaction();

	$ndb = Factory::getInstance()->getNotaDeDebito();
	$ndb->empresa = Funciones::session('empresa');
	$ndb->cliente = Factory::getInstance()->getCliente($idCliente);
	$ndb->tipoDocumento2 = TiposDocumento2::getTipoDocumento2($tipoDocumento2);
	$ndb->observaciones = $observaciones;
	$ndb->tieneDetalle = 'S';

	$esNdr = ($ndb->tipoDocumento2 == 'NDR');

	$ndb->importeNeto = Funciones::toFloat($importeNoGravado + $importeGravado, 2);
	$ndb->descuentoComercialPorc = ($aplicarDescuento ? $ndb->cliente->creditoDescuentoEspecial : 0);
	$descuentoGravado = Funciones::toFloat($importeGravado * $ndb->descuentoComercialPorc / 100, 2);
	$descuentoNoGravado = Funciones::toFloat($importeNoGravado * $ndb->descuentoComercialPorc / 100, 2);
	$ndb->descuentoComercialImporte = $descuentoGravado + $descuentoNoGravado;

	//A cuenta 2 les pongo IVA 0. A las NDB de cheque rechazado le pongo IVA sólo a la comisión, y el NETO va en NoGravado
	$ndb->ivaPorcentaje1 = ($ndb->empresa == 2 ? 0 : $ndb->cliente->condicionIva->porcentajes[1]);

	$ndb->importeNoGravado = Funciones::toFloat($importeNoGravado);

	$arrDetalle = array();

	if ($importeGravado > 0) {
		$item = Factory::getInstance()->getDocumentoItem();
		$item->empresa = $ndb->empresa;
		$item->cliente = $ndb->cliente;
		$item->descripcionItem = $detalleItem;
		$item->numeroDeItem = count($arrDetalle) + 1;
		$item->cantidad = array();
		$item->cantidad[1] = 1;
		$item->precioUnitario = Funciones::toFloat($importeGravado, 2);
		$item->ivaPorcentaje = $ndb->ivaPorcentaje1;
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(($esNdr) ? ParametrosContabilidad::comisionesBancarias : ParametrosContabilidad::deudoresPorVentas)->imputacion;
		$arrDetalle[] = $item;
	}

	if ($importeNoGravado > 0) {
		$item = Factory::getInstance()->getDocumentoItem();
		$item->empresa = $ndb->empresa;
		$item->cliente = $ndb->cliente;
		$item->descripcionItem = $detalleItemNoGravado;
		$item->numeroDeItem = count($arrDetalle) + 1;
		$item->cantidad = array();
		$item->cantidad[1] = 1;
		$item->precioUnitario = Funciones::toFloat($importeNoGravado, 2);
		$item->ivaPorcentaje = 0;
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(($esNdr) ? ParametrosContabilidad::chequesRechazados : ParametrosContabilidad::deudoresPorVentas)->imputacion;
		$arrDetalle[] = $item;
	}

	$ndb->detalle = $arrDetalle;

	$ndb->ivaImporte1 = ($ncr->empresa == 2 ? 0 : Funciones::toFloat(($importeGravado - $descuentoGravado) * $ndb->ivaPorcentaje1 / 100, 2)); //Lo tengo que hacer después del item porque $ncr->subtotal necesita los items para poder calcularse

    Logger::addError('Guardando');
	$ndb->guardar()->notificar('comercial/notas_de_debito/generacion/agregar/');

	Factory::getInstance()->commitTransaction();
	Html::jsonSuccess('La nota de débito fue guardada correctamente');
} catch (Exception $ex){
    Logger::addError($ex->getTraceAsString());
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar guardar la nota de débito ' . $ex->getMessage());
}

?>
<?php } ?>