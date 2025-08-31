<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/documentos_proveedor/documento_proveedor/buscar/')) { ?>
<?php

function getPrecio($precio, $porcentaje) {
	if ($porcentaje) {
		$precioReal = ($precio / (1 + ($porcentaje/100)));
	} else {
		$precioReal = $precio;
	}

	return Funciones::formatearDecimales($precioReal, 4, '.');
}

function jsonDetalleRemito(RemitoPorOrdenDeCompra $remitoPorOrdenDeCompra) {
	$json = array();
	$json['id'] = $remitoPorOrdenDeCompra->id;
	$json['idRemito'] = $remitoPorOrdenDeCompra->remitoProveedor->id;
	$json['idNroItem'] = $remitoPorOrdenDeCompra->remitoProveedorItem->numeroDeItem;
	$json['idImputacion'] = $remitoPorOrdenDeCompra->remitoProveedorItem->remitoProveedor->proveedor->imputacionEspecifica->id;
	$json['imputacion'] = $remitoPorOrdenDeCompra->remitoProveedorItem->remitoProveedor->proveedor->imputacionEspecifica->nombre;
	$json['idMaterialColor'] = $remitoPorOrdenDeCompra->remitoProveedorItem->material->id . ' - ' . $remitoPorOrdenDeCompra->remitoProveedorItem->colorMateriaPrima->idColor;
	$json['descripcion'] = $remitoPorOrdenDeCompra->remitoProveedorItem->material->nombre . ' - Color: ' . $remitoPorOrdenDeCompra->remitoProveedorItem->colorMateriaPrima->idColor;

	if($remitoPorOrdenDeCompra->ordenDeCompraItem->material->usaRango()) {
		$json['cantidades'] = $remitoPorOrdenDeCompra->cantidadesPendientes;
		$json['talles'] = $remitoPorOrdenDeCompra->remitoProveedorItem->material->rango->posicion;
		$json['usaRango'] = 'S';

		$json['cantidad'] = 0;
		$json['total'] = 0;
		$precios = array();
		for($i = 1; $i < 11; $i++) {
			$json['precioUnitario'] = '-';
			$json['cantidad'] += $remitoPorOrdenDeCompra->cantidadesPendientes[$i];
			$precios[$i] = getPrecio($remitoPorOrdenDeCompra->ordenDeCompraItem->precios[$i], $remitoPorOrdenDeCompra->ordenDeCompraItem->impuesto->porcentaje);
			$json['total'] += $remitoPorOrdenDeCompra->cantidadesPendientes[$i] * $precios[$i];
		}
		$json['precios'] = $precios;
	} else {
		$json['cantidad'] = $remitoPorOrdenDeCompra->cantidadPendiente;
		$json['precioUnitario'] = getPrecio($remitoPorOrdenDeCompra->ordenDeCompraItem->precioUnitario, $remitoPorOrdenDeCompra->ordenDeCompraItem->impuesto->porcentaje);
		$json['total'] = $json['cantidad'] * $json['precioUnitario'];
		$json['usaRango'] = 'N';
	}

	return $json;
}

$idRemitoProveedor = Funciones::post('idRemito');
$arr = array();

try {
	$remitoProveedor = Factory::getInstance()->getRemitoProveedor($idRemitoProveedor);

	$where = 'cod_remito_proveedor = ' . Datos::objectToDB($remitoProveedor->id);
	$remitosPorOrdenDeCompra = Factory::getInstance()->getListObject('RemitoPorOrdenDeCompra', $where);

	$arr = array();
	foreach ($remitosPorOrdenDeCompra as $item) {
		/** @var RemitoPorOrdenDeCompra $item */
		if(!$item->aplicado()) {
			$arr[] = jsonDetalleRemito($item);
		}
	}

	if(count($arr) == 0) {
		throw new FactoryExceptionCustomException('No existen detalles por aplicar del remito seleccionado');
	}

	Html::jsonEncode('', $arr);

} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El remito seleccionado no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>