<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/presupuesto/manual/agregar/')) { ?>
<?php

$idProveedor = Funciones::post('idProveedor');
$idLoteDeProduccion = Funciones::post('idLoteDeProduccion');
$tipo = (Funciones::post('tipo') == 'S' || Funciones::post('tipo') == 'N' ? Funciones::post('tipo') : 'N');
$observaciones = Funciones::post('observaciones');
$detalle = Funciones::post('detalle');

try {
	if (empty($idProveedor) || empty($tipo)) {
		throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');
	}

	if (count($detalle) == 0) {
		throw new FactoryExceptionCustomException('El presupuesto debe tener al menos un detalle');
	}

	$detalleAgrupado = array();

	foreach ($detalle as $item) {
		$combinado = $item['idMaterial'] . '_' . $item['idColor'];
		if (empty($detalleAgrupado[$combinado])) {
			$detalleAgrupado[$combinado] = $item;
		} else {
			$detalleAgrupado[$combinado]['cantidad'] += $item['cantidad'];
			for ($i = 1; $i < 16; $i++) {
				$detalleAgrupado[$combinado]['cantidades'][$i] += $item['cantidades'][$i];
			}
		}
	}

	$detalle = $detalleAgrupado;

	Factory::getInstance()->beginTransaction();

	$presupuesto = Factory::getInstance()->getPresupuesto();
	$presupuesto->proveedor = Factory::getInstance()->getProveedor($idProveedor);
	$presupuesto->productivo = $tipo;
	$presupuesto->observaciones = $observaciones;
	$presupuesto->modalidadCreacion = 'M';
	$presupuesto->loteDeProduccion = Factory::getInstance()->getLoteDeProduccion($idLoteDeProduccion);

	foreach ($detalle as $item) {
		if (empty($item['idMaterial']) || empty($item['idColor']) || empty($item['fechaEntrega'])) {
			throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios de los detalles');
		}
		if ($item['cantidad'] < 0) {
			throw new FactoryExceptionCustomException('Las cantidades no pueden ser menores a 0');
		}

		try {
			$ProveedorMateriaPrima = Factory::getInstance()->getProveedorMateriaPrima($idProveedor, $item['idMaterial'], $item['idColor']);
		} catch (FactoryExceptionRegistroNoExistente $ex) {
			throw new FactoryExceptionCustomException('El proveedor "' . $presupuesto->proveedor->razonSocial . '" no trabaja el material "' . Factory::getInstance()->getMaterial($item['idMaterial'])->nombre . '"');
		}

		$presupuestoItem = Factory::getInstance()->getPresupuestoItem();

		$presupuestoItem->cantidad = $item['cantidad'];
		$cantidades = $item['cantidades'];

		$cantidadTotal = 0;
		for ($i = 1; $i < 11; $i++) {
			$presupuestoItem->cantidades[$i] = Funciones::toInt($cantidades[$i]['cantidad']);

			if ($presupuestoItem->cantidades[$i]['cantidad'] < 0) {
				throw new FactoryExceptionCustomException('Las cantidades no pueden ser menores a 0');
			}
			$cantidadTotal += $cantidades[$i]['cantidad'];
		}

		if ($cantidadTotal <= 0) {
			throw new FactoryExceptionCustomException('No puede ingresar cantidades menores o iguales a 0');
		}

		if ($cantidadTotal != $presupuestoItem->cantidad) {
			throw new FactoryExceptionCustomException('La cantidad total no se corresponde con los detalles');
		}

		$presupuestoItem->colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($item['idMaterial'], $item['idColor']);
		$presupuestoItem->material = $presupuestoItem->colorMateriaPrima->material;
		$presupuestoItem->fechaEntrega = $item['fechaEntrega'];

		$presupuesto->addDetalle($presupuestoItem);
	}
	$presupuesto->guardar();
	Factory::getInstance()->commitTransaction();
	Html::jsonSuccess('El pedido de cotización se agregó correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar agregar el pedido de cotización');
}

?>
<?php } ?>