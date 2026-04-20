<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/generica/agregar/')) { ?>
<?php

$idCliente = Funciones::post('idCliente');
$tipoDocumento2 = Funciones::post('tipoDocumento2');
$idCausa = Funciones::post('idCausa');
$detalleItem = Funciones::post('detalleItem');
$detalleItemNoGravado = Funciones::post('detalleItemNoGravado');
$observaciones = Funciones::post('observaciones');
$importeNoGravado = Funciones::post('importeNoGravado');
$importeGravado = Funciones::post('importeGravado');
$aplicarDescuento = Funciones::post('aplicarDescuento') == 'S';

try {
	Factory::getInstance()->beginTransaction();

	$ncr = Factory::getInstance()->getNotaDeCredito();
	$ncr->empresa = Funciones::session('empresa');
	$ncr->cliente = Factory::getInstance()->getCliente($idCliente);
	$ncr->tipoDocumento2 = TiposDocumento2::getTipoDocumento2($tipoDocumento2);
	$ncr->causa = Factory::getInstance()->getCausaNotaDeCredito($idCausa);
	$ncr->observaciones = $observaciones;
	$ncr->tieneDetalle = 'S';

	$ncr->importeNeto = Funciones::toFloat($importeNoGravado + $importeGravado, 2);
	$ncr->descuentoComercialPorc = ($aplicarDescuento ? $ncr->cliente->creditoDescuentoEspecial : 0);
	$descuentoGravado = Funciones::toFloat($importeGravado * $ncr->descuentoComercialPorc / 100, 2);
	$descuentoNoGravado = Funciones::toFloat($importeNoGravado * $ncr->descuentoComercialPorc / 100, 2);
	$ncr->descuentoComercialImporte = $descuentoGravado + $descuentoNoGravado;

	//A cuenta 2 le pongo IVA 0
	$ncr->ivaPorcentaje1 = ($ncr->empresa == 2 ? 0 : $ncr->cliente->condicionIva->porcentajes[1]);

	$ncr->importeNoGravado = Funciones::toFloat($importeNoGravado);

	$arrDetalle = array();

	if ($importeGravado > 0) {
		$item = Factory::getInstance()->getDocumentoItem();
		$item->empresa = $ncr->empresa;
		$item->cliente = $ncr->cliente;
		$item->descripcionItem = $detalleItem;
		$item->numeroDeItem = count($arrDetalle) + 1;
		$item->cantidad = array();
		$item->cantidad[1] = 1;
		$item->precioUnitario = Funciones::toFloat($importeGravado, 2);
		$item->ivaPorcentaje = $ncr->ivaPorcentaje1;
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ingresosPorVentas)->imputacion;
		$arrDetalle[] = $item;
	}

	if ($importeNoGravado > 0) {
		$item = Factory::getInstance()->getDocumentoItem();
		$item->empresa = $ncr->empresa;
		$item->cliente = $ncr->cliente;
		$item->descripcionItem = $detalleItemNoGravado;
		$item->numeroDeItem = count($arrDetalle) + 1;
		$item->cantidad = array();
		$item->cantidad[1] = 1;
		$item->precioUnitario = Funciones::toFloat($importeNoGravado, 2);
		$item->ivaPorcentaje = 0;
		$item->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ingresosPorVentas)->imputacion;
		$arrDetalle[] = $item;
	}

	$ncr->detalle = $arrDetalle;

	$ncr->ivaImporte1 = ($ncr->empresa == 2 ? 0 : Funciones::toFloat(($importeGravado - $descuentoGravado) * $ncr->ivaPorcentaje1 / 100, 2)); //Lo tengo que hacer después del item porque $ncr->subtotal necesita los items para poder calcularse

	$ncr->guardar()->notificar('comercial/notas_de_credito/generacion/generica/agregar/');

	Factory::getInstance()->commitTransaction();
	Html::jsonSuccess('La nota de crédito fue guardada correctamente');
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar guardar la nota de crédito');
}

?>
<?php } ?>