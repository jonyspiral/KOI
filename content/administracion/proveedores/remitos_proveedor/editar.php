<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/remitos_proveedor/editar/')) { ?>
<?php

function getOrdenDeCompra($item) {
	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'cod_orden_de_compra = ' . Datos::objectToDB($item['idOrdenDeCompra']) . ' AND ';
	$where .= 'cod_material = ' . Datos::objectToDB($item['idMaterial']) . ' AND ';
	$where .= 'cod_color = ' . Datos::objectToDB($item['idColor']);
	$ordenDeCompraItem = Factory::getInstance()->getListObject('OrdenDeCompraItem', $where);
	if (count($ordenDeCompraItem) != 1) {
		throw new FactoryExceptionCustomException('La órden de compra Nº "' . $item['idOrdenDeCompra'] . '" no tiene en sus detalles a la materia prima "' . $item['idMaterial'] . ' - ' . $item['idColor'] . '"');
	}
	if($ordenDeCompraItem[0]->cantidadPendiente <= 0){
		throw new FactoryExceptionCustomException('La órden de compra Nº "' . $item['idOrdenDeCompra'] . '" ya tiene saciada la materia prima "' . $item['idMaterial'] . ' - ' . $item['idColor'] . '"');
	}

	return $ordenDeCompraItem[0];
}

$idRemito = Funciones::post('idRemito');
$detalleRemito = Funciones::post('detalle');
$confirmar = (Funciones::get('confirmar') == '1');

try {
	if (!isset($idRemito))
		throw new FactoryExceptionRegistroNoExistente();

	if (count($detalleRemito) == 0) {
		throw new FactoryExceptionCustomException('El remito debe tener al menos un detalle');
	}

	Factory::getInstance()->beginTransaction();

	$remito = Factory::getInstance()->getRemitoProveedor($idRemito);

	if($remito->esHexagono()){
		throw new FactoryExceptionCustomException('No se pueden editar remitos que fueron creados en el sistema Hexágono');
	}

	$mensaje = 'Los siguientes materiales exceden la cantidad que figura en órdenes de compra:';
	$hayQueConfirmar = false;
	$remitoNuevo = Factory::getInstance()->getRemitoProveedor();
	$remitoNuevo->proveedor = $remito->proveedor;
	$remitoNuevo->fechaRecepcion = $remito->fechaRecepcion;
	$remitoNuevo->sucursal = $remito->sucursal;
	$remitoNuevo->numero = $remito->numero;

	$remito->borrar();

	$remitos = Factory::getInstance()->getListObject('RemitoProveedor', 'cod_proveedor = ' . Datos::objectToDB($remitoNuevo->proveedor->id) . ' AND nro_compuesto_remito = ' . Datos::objectToDB($remitoNuevo->nroCompuestoRemito));
	if (count($remitos) > 0) {
		throw new FactoryExceptionCustomException('El numero de remito "' . Funciones::padLeft($ptoVenta, 4, 0) . '-' . Funciones::padLeft($numero, 8, 0) . '" del proveedor "' . $remitoNuevo->proveedor->id . '" ya existe en el sistema');
	}

	$arrayCantidadSobrante = array();
	foreach($detalleRemito as $item) {
		$key = $item['idMaterial'] . '_' . $item['idColor'];
		if (empty($detalleRemitoAgrupado[$key])) {
			$remitoItem = Factory::getInstance()->getRemitoProveedorItem();
		} else {
			$remitoItem = $detalleRemitoAgrupado[$key];
		}

		$remitoItem->colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($item['idMaterial'], $item['idColor']);
		$remitoItem->material = $remitoItem->colorMateriaPrima->material;
		$remitoItem->cantidad += $item['cantidad'];

		$cantidad = $item['cantidad'];
		$cantidades = array();

		$cantidadTotal = 0;
		for ($i = 1; $i < 11; $i++) {
			$remitoItem->cantidades[$i] += Funciones::toInt($item['cantidades'][$i]);
			$cantidades[$i] = Funciones::toInt($item['cantidades'][$i]);
			if ($remitoItem->cantidades[$i] < 0) {
				throw new FactoryExceptionCustomException('Las cantidades no pueden ser menores a 0');
			}
			$cantidadTotal += $remitoItem->cantidades[$i];
		}

		if(Factory::getInstance()->getMaterial($item['idMaterial'])->usaRango()) {
			if ($cantidadTotal != $remitoItem->cantidad) {
				throw new FactoryExceptionCustomException('La cantidad total no se corresponde con los detalles');
			}
		}

		$ordenDeCompraItem = getOrdenDeCompra($item);
		/** @var OrdenDeCompraItem $ordenDeCompraItem */
		$remitoPorOrdenDeCompra = Factory::getInstance()->getRemitoPorOrdenDeCompra();
		$remitoPorOrdenDeCompra->ordenDeCompra = $ordenDeCompraItem->ordenDeCompra;
		$remitoPorOrdenDeCompra->numeroDeItemOrdenDeCompra = $ordenDeCompraItem->numeroDeItem;

		if ($ordenDeCompraItem->cantidadPendiente >= $item['cantidad']) {
			$ordenDeCompraItem->cantidadPendiente -= $item['cantidad'];
			$cantidadOc = $item['cantidad'];
		} else {
			$cantidadOc = $ordenDeCompraItem->cantidadPendiente;
			if(!$ordenDeCompraItem->material->usaRango()){
				$arrayCantidadSobrante[$key] = array();
				$arrayCantidadSobrante[$key]['hayExcedente'] = true;
				$arrayCantidadSobrante[$key]['idMaterial'] = $item['idMaterial'];
				$arrayCantidadSobrante[$key]['idColor'] = $item['idColor'];
				$arrayCantidadSobrante[$key]['cantidad'] += $item['cantidad'] - $ordenDeCompraItem->cantidadPendiente;
				$arrayCantidadSobrante[$key]['usaRango'] = false;
			}
			$ordenDeCompraItem->cantidadPendiente = 0;
		}

		if ($ordenDeCompraItem->material->usaRango()) {
			$cantidadesOc = array();
			$arrayCantidadSobrante[$key]['cantidades'] = array();
			$arrayCantidadSobrante[$key]['idMaterial'] = $item['idMaterial'];
			$arrayCantidadSobrante[$key]['idColor'] = $item['idColor'];
			for ($i = 1; $i < 11; $i++) {
				if ($ordenDeCompraItem->cantidades[$i] == 0 && $item['cantidades'][$i] > 0) {
					throw new FactoryExceptionCustomException('La órden de compra Nº "' . $item['idOrdenDeCompra'] . '" no tiene en sus detalles el talle de la posición ' . $i . ' para la materia prima "' . $item['idMaterial'] . ' - ' . $item['idColor'] . '"');
				}

				if ($item['cantidades'][$i] > 0) {
					if ($ordenDeCompraItem->cantidadesPendientes[$i] >= $item['cantidades'][$i]) {
						$ordenDeCompraItem->cantidadesPendientes[$i] -= $item['cantidades'][$i];
						$cantidadesOc[$i] = $item['cantidades'][$i];
					} else {
						$cantidadesOc[$i] = $ordenDeCompraItem->cantidadesPendientes[$i];
						$arrayCantidadSobrante[$key]['hayExcedente'] = true;
						$arrayCantidadSobrante[$key]['cantidades'][$i] += $item['cantidades'][$i] - $ordenDeCompraItem->cantidadesPendientes[$i];
						$arrayCantidadSobrante[$key]['usaRango'] = true;
						$ordenDeCompraItem->cantidadesPendientes[$i] = 0;
					}
				}
			}
		} else {
			$remitoItem->cantidades[1] = $cantidad;
			$cantidadesOc[1] = 0;
			$cantidades[1] = 0;
		}

		$ordenDeCompraItem->guardar();
		$remitoPorOrdenDeCompra->cantidadOc = $cantidadOc;
		$remitoPorOrdenDeCompra->cantidadesOc = $cantidadesOc;
		$remitoPorOrdenDeCompra->cantidad = $cantidad;
		$remitoPorOrdenDeCompra->cantidades = $cantidades;
		$remitoItem->addRemitoPorOrdenDeCompra($remitoPorOrdenDeCompra);

		$detalleRemitoAgrupado[$key] = $remitoItem;
	}

	$remitoNuevo->detalle = $detalleRemitoAgrupado;

	foreach ($arrayCantidadSobrante as $value) {
		if($value['hayExcedente']) {
			$hayQueConfirmar = true;
			$colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($value['idMaterial'], $value['idColor']);
			$mensajeParcial = $colorMateriaPrima->material->nombre . ' - ' . $colorMateriaPrima->idColor . ': ';
			if ($value['usaRango']) {
				$restanCantidades = $value['cantidades'];
				$mensajeParcial .= '[';
				for ($i = 1; $i < 11; $i++) {
					if ($restanCantidades[$i] > 0) {
						$mensajeParcial .= $colorMateriaPrima->material->rango->posicion[$i] . ': ' . $restanCantidades[$i] . ', ';
					}
				}
				$mensaje .= '<br>' . trim($mensajeParcial, ', ') . ']';
			} else {
				$mensaje .= '<br>' . $mensajeParcial . $value['cantidad'];
			}
		}
	}

	if (!$confirmar && $hayQueConfirmar) {
		Factory::getInstance()->rollbackTransaction();
		$mensaje .= '<br>¿Quiere generar el remito de todos modos?';
		Html::jsonConfirm($mensaje, 'confirmar');
	} else {
		$remitoNuevo->guardar();
		Factory::getInstance()->commitTransaction();
		Html::jsonSuccess('El remito se agregó correctamente');
	}
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el remito');
}

?>
<?php } ?>