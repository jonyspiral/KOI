<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/generacion/agregar/')) { ?>
<?php

$detalle = Funciones::post('detalle');
$idProveedor = Funciones::post('idProveedor');
$idLoteDeProduccion = Funciones::post('idLoteDeProduccion');
$tieneIva = Funciones::post('tieneIva') == 'S';
$observaciones = Funciones::post('observaciones');

try {
	Factory::getInstance()->beginTransaction();

	$ordenDeCompra = Factory::getInstance()->getOrdenDeCompra();
	$ordenDeCompra->proveedor = Factory::getInstance()->getProveedor($idProveedor);
	$ordenDeCompra->loteDeProduccion = Factory::getInstance()->getLoteDeProduccion($idLoteDeProduccion);
	$ordenDeCompra->observaciones = $observaciones;

	$presupuestosItem = array();
	$arrayItems = array();
	foreach($detalle as $item){
		$presupuestoItem = Factory::getInstance()->getPresupuestoItem($item['id']['idPresupuesto'], $item['id']['nroItem']);

		if($presupuestoItem->saciado()){
			throw new FactoryExceptionCustomException('No puede utilizar detalles saciados de presupuestos');
		}

		$keyArrayItems = $presupuestoItem->presupuesto->proveedor->id . '_' . $presupuestoItem->colorMateriaPrima->material->id . '_' . $presupuestoItem->colorMateriaPrima->idColor;
		if($arrayItems[$keyArrayItems]){
			throw new FactoryExceptionCustomException('La combinación material - color no puede repetirse. El material "' . $presupuestoItem->colorMateriaPrima->material->id . ' - ' . $presupuestoItem->colorMateriaPrima->idColor . '" se especifica más de una vez');
		}

		$arrayItems[$keyArrayItems] = true;

		$todosUsanMismoPrecioUnitario = true;

		$presupuestoItem->saciar();
		$presupuestoItem->guardar();
		$presupuestosItem[] = $presupuestoItem;

		$ordenDeCompraItem = Factory::getInstance()->getOrdenDeCompraItem();
		$ordenDeCompraItem->material = $presupuestoItem->colorMateriaPrima->material;
		$ordenDeCompraItem->idColorMaterial = $presupuestoItem->colorMateriaPrima->idColor;
		$ordenDeCompraItem->fechaEntrega = $presupuestoItem->fechaEntrega;
		$ordenDeCompraItem->cantidad = $presupuestoItem->cantidad;
		$ordenDeCompraItem->fechaEntrega = $presupuestoItem->fechaEntrega;

		if(!empty($item['idImpuesto'])){
			$impuesto = Factory::getInstance()->getImpuesto($item['idImpuesto']);
			$porcImpuesto = $impuesto->porcentaje/100;
		} else{
			$porcImpuesto = 0;
		}
		if($ordenDeCompraItem->material->usaRango()){
			$total = 0;
			$precio = $item['precios'][1];
			$precioMaximo = 0;
			for($i = 1; $i < 11; $i++){
				if(empty($item['precios'][$i]) && !empty($presupuestoItem->cantidades[$i])){
					throw new FactoryExceptionCustomException('Debe completar los precios de los artículos con unidades pedidas');
				}

				$ordenDeCompraItem->cantidades[$i] = $presupuestoItem->cantidades[$i];

				if(!empty($presupuestoItem->cantidades[$i])){
					//$ordenDeCompraItem->precios[$i] = $item['precios'][$i] - ($item['precios'][$i] * $porcImpuesto);
					$ordenDeCompraItem->precios[$i] = $item['precios'][$i];
				}

				$total += $ordenDeCompraItem->cantidades[$i] * $ordenDeCompraItem->precios[$i];

				if($precio != $ordenDeCompraItem->precios[$i] && !empty($ordenDeCompraItem->cantidades[$i])){
					$todosUsanMismoPrecioUnitario = false;
				}

				if ($ordenDeCompraItem->precios[$i] > $precioMaximo) {
					$precioMaximo = $ordenDeCompraItem->precios[$i];
				}
			}

			$precioParaCambiar = $precioMaximo;
		} else {
			if(empty($item['precios'])){
				throw new FactoryExceptionCustomException('Debe completar los precios de los artículos con unidades pedidas');
			}
			$ordenDeCompraItem->precioUnitario = $item['precios'];
			$total = $ordenDeCompraItem->cantidad * $ordenDeCompraItem->precioUnitario;
			$todosUsanMismoPrecioUnitario = false;

			$precioParaCambiar = $ordenDeCompraItem->precioUnitario;
		}

		$ordenDeCompraItem->importe = $total;

		if($todosUsanMismoPrecioUnitario){
			$ordenDeCompraItem->precioUnitario = $precio;
		}

		if(!empty($item['idImpuesto'])){
			$ordenDeCompraItem->impuesto = $impuesto;
			$ordenDeCompraItem->importeImpuesto = $total * ($porcImpuesto/(1 + $porcImpuesto));
		}

		$ordenDeCompra->addDetalle($ordenDeCompraItem);

		$precioParaCambiar = Funciones::formatearDecimales($precioParaCambiar / (1 + $porcImpuesto), 4, '.');

		if (!empty($item['cambiarPrecioProveedor'])) {
			if ($item['cambiarPrecioProveedor'] == '1') {
				$proveedorMateriaPrima = Factory::getInstance()->getProveedorMateriaPrima($idProveedor, $ordenDeCompraItem->material->id, $ordenDeCompraItem->colorMateriaPrima->idColor);
				$proveedorMateriaPrima->precioCompra = $precioParaCambiar;
				$proveedorMateriaPrima->guardar();
			} elseif ($item['cambiarPrecioProveedor'] == '2') {
				$proveedorMateriaPrimas = Factory::getInstance()->getListObject('ProveedorMateriaPrima', 'cod_proveedor = ' . Datos::objectToDB($idProveedor) .  ' AND cod_material = ' . Datos::objectToDB($ordenDeCompraItem->material->id) . ' AND anulado = ' . Datos::objectToDB('N'));
				foreach ($proveedorMateriaPrimas as $proveedorMateriaPrima) {
					/** @var ProveedorMateriaPrima $proveedorMateriaPrima */
					$proveedorMateriaPrima->precioCompra = $precioParaCambiar;
					$proveedorMateriaPrima->guardar();
				}
			} else {
				throw new FactoryExceptionCustomException('Opción inválida para cambio de precio proveedor');
			}
		}

		if (!empty($item['cambiarPrecio'])) {
			if ($item['cambiarPrecio'] == '1') {
				$ordenDeCompraItem->colorMateriaPrima->precioUnitario = $precioParaCambiar;
				$ordenDeCompraItem->colorMateriaPrima->guardar();
			} elseif ($item['cambiarPrecio'] == '2') {
				$materiasPrimas = Factory::getInstance()->getListObject('ColorMateriaPrima', 'cod_material = ' . Datos::objectToDB($ordenDeCompraItem->material->id) . ' AND anulado = ' . Datos::objectToDB('N'));
				foreach ($materiasPrimas as $materiaPrima) {
					/** @var ColorMateriaPrima $materiaPrima */
					$materiaPrima->precioUnitario = $precioParaCambiar;
					$materiaPrima->guardar();
				}
			} else {
				throw new FactoryExceptionCustomException('Opción inválida para cambio de precio costo');
			}
		}
	}

	$ordenDeCompra->guardar();

	for($i = 0; $i < count($ordenDeCompra->detalle); $i++){
		$presupuestoOrdenCompra = Factory::getInstance()->getPresupuestoOrdenCompra();

		$presupuestoOrdenCompra->ordenDeCompraItem = $ordenDeCompra->detalle[$i];
		$presupuestoOrdenCompra->presupuestoItem = $presupuestosItem[$i];

		$presupuestoOrdenCompra->guardar();
	}

	Factory::getInstance()->commitTransaction();
	Html::jsonSuccess('Se agregó correctamente la orden de compra', array('id' => $ordenDeCompra->id));
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar la orden de compra');
}
?>
<?php } ?>