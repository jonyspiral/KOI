<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/calidad/garantias/agregar/')) { ?>
<?php

$items = Funciones::post('items');
$orderId = Funciones::post('orderId') == 'false' ? false : Funciones::post('orderId');
$idCliente = Funciones::post('idCliente');
$idMotivo = Funciones::post('idMotivo');
$observaciones = Funciones::post('observaciones');

function agruparArticulos($items) {
	$ajustesAgrupados = array();

	foreach ($items as $item) {
		if ($item['idArticulo'] && $item['idColor']) {
			$combinado = $item['idArticulo'] . '_' . $item['idColor'];
			if (!$ajustesAgrupados[$combinado]) {
				$ajustesAgrupados[$combinado] = $item;
			} else {
				for ($i = 1; $i <= 10; $i++) {
					$ajustesAgrupados[$combinado]['cantidades'][$i] += Funciones::toInt($item['cantidades'][$i]);
				}
				$ajustesAgrupados[$combinado]['total'] += $item['total'];
			}
		}
	}

	return $ajustesAgrupados;
}

function getPrecioPorItem(Garantia $garantia, GarantiaItem $item) {
	if (isset($garantia->cliente->id)) {
		//Intento obtener el último precio al que se le vendió
		$where = 'cod_cliente = ' . Datos::objectToDB($garantia->cliente->id);
		$where .= ' AND cod_articulo = ' . Datos::objectToDB($item->articulo->id);
		$where .= ' AND cod_color_articulo = ' . Datos::objectToDB($item->colorPorArticulo->id);
		$order = ' ORDER BY fecha_alta DESC';
		$despachos = Factory::getInstance()->getListObject('DespachoItem', $where . $order, 1);
		if (count($despachos)) {
			/** @var DespachoItem $despacho */
			$despacho = $despachos[0];
			return $despacho->precioUnitario * $item->cantidadTotal;
		}
	} else {
		foreach ($garantia->order->despacho->detalle as $detalle) {
			if (($detalle->idArticulo == $item->articulo->id) && ($detalle->idColorPorArticulo == $item->colorPorArticulo->id)) {
				return $detalle->precioUnitario * $item->cantidadTotal;
			}
		}
	}
	//Si no logro obtener un precio al que se le haya vendido alguna vez, devuelvo el precio actual del artículo (según el cliente)
	return $item->colorPorArticulo->getPrecioSegunCliente($garantia->cliente) * $item->cantidadTotal;
}

try {
	if (!$orderId && !$idCliente) {
		throw new FactoryExceptionCustomException('Debe seleccionar el cliente');
	}
	if (!$idMotivo) {
		throw new FactoryExceptionCustomException('Debe elegir un motivo para la creación de la garantía');
	}

	/** @var Garantia $garantia */
	$garantia = Factory::getInstance()->getGarantia();
	$garantia->motivo = Factory::getInstance()->getMotivo($idMotivo);
	$garantia->observaciones = $observaciones;

	$itemsOrder = array();
	if ($orderId) {
		$order = Factory::getInstance()->getEcommerce_Order($orderId);
		$garantia->order = $order;
		foreach ($garantia->order->details as $detalle) {
			$itemsOrder[] = $detalle->reference;
		}
	} else {
		$cliente = Factory::getInstance()->getCliente($idCliente);
		$garantia->cliente = $cliente;
	}

	$items = agruparArticulos($items);
	$almacen = Factory::getInstance()->getAlmacen(Garantia::ALMACEN_INGRESO_GARANTIA);

	$importeTotal = 0;
	foreach ($items as $item) {
		$garantiaItem = Factory::getInstance()->getGarantiaItem();
		$garantiaItem->almacen = $almacen;
		$garantiaItem->articulo = Factory::getInstance()->getArticulo($item['idArticulo']);
		$garantiaItem->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($item['idArticulo'], $item['idColor']);
		for ($i = 1; $i <= 10; $i++) {
			$garantiaItem->cantidad[$i] = $item['cantidades'][$i];
			if ($orderId && !in_array($garantiaItem->articulo->id . $garantiaItem->colorPorArticulo->id, $itemsOrder)) {
				throw new FactoryExceptionCustomException('No se puede ingresar el artículo ' . $garantiaItem->articulo->id . '-' . $garantiaItem->colorPorArticulo->id . ' ya que no es parte del pedido de ecommerce');
			}
		}
		$garantiaItem->importeNcr = getPrecioPorItem($garantia, $garantiaItem);
		$importeTotal += $garantiaItem->importeNcr;
		$garantia->addItem($garantiaItem);
	}

	$garantia->totalNcr = $importeTotal;

	$garantia->guardar()->notificar('comercial/calidad/garantias/agregar/');

	Html::jsonSuccess('Se guardó correctamente la garantía');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la garantía');
}

?>
<?php } ?>