<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/devolucion/agregar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$idCliente = Funciones::post('idCliente');
$idCausa = Funciones::post('idCausa');
$observaciones = Funciones::post('observaciones');
$detalleNcr = Funciones::post('articulos');

try {
	Factory::getInstance()->beginTransaction();

	$ncr = Factory::getInstance()->getNotaDeCredito();
	$ncr->empresa = $empresa;
	$ncr->cliente = Factory::getInstance()->getCliente($idCliente);
	$ncr->tipoDocumento2 = TiposDocumento2::ncrDevolucion;
	$ncr->causa = Factory::getInstance()->getCausaNotaDeCredito($idCausa);
	$ncr->observaciones = $observaciones;
	$ncr->tieneDetalle = 'N';

	$arrDetalle = array();
	$nroItem = 1;
	foreach ($detalleNcr as $item) {
		$arr = explode('_', $item['precio']);
		$despachoItem = Factory::getInstance()->getDespachoItem($arr[0], $arr[1]);
		$precio = $despachoItem->precioUnitario;

		$color = Factory::getInstance()->getColorPorArticulo($item['articulo'], $item['color']);
		if (empty($precio)) {
			throw new FactoryExceptionCustomException('No puede devolver este artículo ya que el cliente nunca antes lo compró');
			//$precio = $color->getPrecioSegunCliente($ncr->cliente);
		}

		$documentoItem = Factory::getInstance()->getDocumentoItem();
		$documentoItem->cliente = $ncr->cliente; //Sirve para calcular el IVA
		$documentoItem->almacen = Factory::getInstance()->getAlmacen($item['almacen']);
		$documentoItem->articulo = Factory::getInstance()->getArticulo($item['articulo']);
		$documentoItem->colorPorArticulo = $color;
		for ($j = 1; $j <= 8; $j++) {
			$documentoItem->cantidad[$j] = $item['cantidades'][$j]; 
		}
		$documentoItem->empresa = $empresa;
		$documentoItem->numeroDeItem = $nroItem;
		$documentoItem->ivaPorcentaje = $documentoItem->getPorcentajeIva();
		$documentoItem->precioUnitario = Funciones::toFloat($precio);
		$documentoItem->imputacion = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ingresosPorVentas)->imputacion;
		$arrDetalle[] = $documentoItem;
		$nroItem++;
	}
	$ncr->detalle =  $arrDetalle;

	//Calculo el importe NETO
	$importeNeto = 0;
	foreach ($ncr->detalle as $item) {
		$importeNeto += $item->cantidadTotal * $item->precioUnitario;
	}
	$ncr->importeNeto = Funciones::toFloat($importeNeto);

	$ncr->descuentoComercialPorc = $ncr->cliente->creditoDescuentoEspecial;
	$ncr->descuentoComercialImporte = Funciones::toFloat($ncr->importeNeto * $ncr->descuentoComercialPorc / 100, 2);

	if ($ncr->empresa == 2) {
		$ncr->ivaPorcentaje1 = 0;
		$ncr->ivaPorcentaje2 = 0;
		$ncr->ivaPorcentaje3 = 0;
		$ncr->ivaImporte1 = 0;
		$ncr->ivaImporte2 = 0;
		$ncr->ivaImporte3 = 0;
	} else {
		$iva = array();
		foreach ($ncr->detalle as $item) {
			if (!isset($iva[Funciones::toString($item->ivaPorcentaje)]))
				$iva[Funciones::toString($item->ivaPorcentaje)] = 0;
			$iva[Funciones::toString($item->ivaPorcentaje)] += Funciones::toFloat($item->precioUnitario * $item->cantidadTotal);
		}
		if (isset($iva['21'])) //Hardcodeo. Es el porcentaje en el cual se aplica el descuento comercial
			$iva['21'] = Funciones::toFloat($iva['21'] - $ncr->descuentoComercialImporte);
		$j = 1;
		foreach ($iva as $porc => $valor) {
			$attr1 = 'ivaPorcentaje' . $j;
			$attr2 = 'ivaImporte' . $j;
			$ncr->$attr1 = Funciones::toFloat($porc);
			$ncr->$attr2 = Funciones::toFloat($valor * (Funciones::toFloat($porc) / 100));
			$j++;
		}
	}
	$ncr->importeNoGravado = 0;

	$ncr->guardar()->notificar('comercial/notas_de_credito/generacion/devolucion/agregar/');

	Factory::getInstance()->commitTransaction();
	Html::jsonSuccess('Se generaron correctamente ' . $i . ' nota(s) de crédito');
} catch (FactoryExceptionCustomException $ex) {
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar generar la(s) nota(s) de crédito');
}

?>
<?php } ?>