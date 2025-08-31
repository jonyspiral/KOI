<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/presupuesto/generacion/agregar/')) { ?>
<?php

$detalle = Funciones::post('detalle');
$idProveedor = Funciones::post('idProveedor');
$observaciones = Funciones::post('observaciones');

try {
	Factory::getInstance()->beginTransaction();

	$ordenDeCompra = Factory::getInstance()->getOrdenDeCompra();
	$ordenDeCompra->proveedor = Factory::getInstance()->getProveedor($idProveedor);
	$ordenDeCompra->observaciones = $observaciones;

	$presupuestosItem = array();
	$almacen = Factory::getInstance()->getPresupuestoItem($detalle[0]['idPresupuesto'], $detalle[0]['nroItem'])->presupuesto->almacen;
	foreach($detalle as $item){
		$presupuestoItem = Factory::getInstance()->getPresupuestoItem($item['idPresupuesto'], $item['nroItem']);

		if($presupuestoItem->saciado()){
			throw new FactoryExceptionCustomException('No puede utilizar detalles saciados de presupuestos');
		}

		$todosUsanMismoPrecioUnitario = true;

		$presupuestoItem->saciar();
		$presupuestoItem->guardar();
		$presupuestosItem[] = $presupuestoItem;

		$ordenDeCompraItem = Factory::getInstance()->getOrdenDeCompraItem();
		$ordenDeCompraItem->material = $presupuestoItem->colorMateriaPrima->material;
		$ordenDeCompraItem->idColorMaterial = $presupuestoItem->colorMateriaPrima->idColor;
		$ordenDeCompraItem->fechaEntrega = $presupuestoItem->fechaEntrega;
		$ordenDeCompraItem->cantidad = $presupuestoItem->cantidad;
		$ordenDeCompraItem->precioUnitario = $presupuestoItem->precioUnitario;
		$ordenDeCompraItem->fechaEntrega = $presupuestoItem->fechaEntrega;

		if($almacen->id != $presupuestoItem->presupuesto->almacen->id){
			throw new FactoryExceptionCustomException('No puede generar una órden de compra con presupuestos de distintos almacenes');
		}

		$almacen = $presupuestoItem->presupuesto->almacen;

		if($ordenDeCompraItem->material->usaRango()){
			$total = 0;
			$precio = $ordenDeCompraItem->precios[1];
			for($i = 1; $i < 16; $i++){
				$ordenDeCompraItem->cantidades[$i] = $presupuestoItem->cantidades[$i];
				$ordenDeCompraItem->precios[$i] = $presupuestoItem->precios[$i];
				$total += $presupuestoItem->cantidades[$i] * $presupuestoItem->precios[$i];

				if($precio != $ordenDeCompraItem->precios[$i]){
					$todosUsanMismoPrecioUnitario = false;
				}
			}
		} else{
			$total = $presupuestoItem->cantidad * $presupuestoItem->precioUnitario;
		}

		$ordenDeCompraItem->importe = $total;

		if($todosUsanMismoPrecioUnitario){
			$ordenDeCompraItem->precioUnitario = $precio;
		}

		$ordenDeCompra->addDetalle($ordenDeCompraItem);
	}

	$ordenDeCompra->almacen = $almacen;
	$ordenDeCompra->guardar();

	for($i = 0; $i < count($ordenDeCompra->detalle); $i++){
		$presupuestoOrdenCompra = Factory::getInstance()->getPresupuestoOrdenCompra();

		$presupuestoOrdenCompra->ordenDeCompraItem = $ordenDeCompra->detalle[$i];
		$presupuestoOrdenCompra->presupuestoItem = $presupuestosItem[$i];

		$presupuestoOrdenCompra->guardar();
	}

	Factory::getInstance()->commitTransaction();
	Html::jsonSuccess('Se agregó correctamente la orden de compra');
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar la orden de compra');
}
?>
<?php } ?>