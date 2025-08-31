<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/generacion/buscar/')) { ?>
<?php

function jsonArrayDetalles($detalles) {
	$resultado = array();

	foreach($detalles as $item){
		/** @var PresupuestoItem $item */
		$proveedorMateriaPrima = Factory::getInstance()->getProveedorMateriaPrima($item->presupuesto->idProveedor, $item->idMaterial, $item->idColorMaterial);
		$arr = array();
		$arr['idPresupuesto'] = $item->idPresupuesto;
		$arr['nroItem'] = $item->numeroDeItem;
		$arr['idLoteDeProduccion'] = $item->presupuesto->idLoteDeProduccion;
		$arr['fechaEntrega'] = $item->fechaEntrega;
		$arr['idMaterial'] = $item->colorMateriaPrima->material->id;
		$arr['nombreMaterial'] = $item->colorMateriaPrima->material->nombre;
		$arr['precioProveedorMaterial'] = $proveedorMateriaPrima->precioCompra;
		$arr['precioCabeceraMaterial'] = $item->colorMateriaPrima->precioUnitario;
		$arr['idColor'] = $item->colorMateriaPrima->idColor;
		$arr['nombreColor'] = $item->colorMateriaPrima->nombreColor;
		$arr['cantidad'] = $item->cantidad;
		$arr['usaRango'] = $item->colorMateriaPrima->material->usaRango;

		$arrItemPos = array();
        $cantidadTotal = 0;
		for($i = 1; $i < 11; $i++){
			$cantItem = $item->cantidades[$i];
			$talleItem = $item->material->rango->posicion[$i];
			$cantItem = (empty($talleItem) ? '' : (empty($cantItem) ? 0 : $cantItem));
			$talleItem = (empty($talleItem) ? '---' : $talleItem);
            $cantidadTotal += $item->cantidades[$i];
			$itemPos = array('talle' => $talleItem, 'cantidad' => $cantItem);
			$arrItemPos[$i] = $itemPos;
		}
		$arrItemPos[11] = array('talle' => 'Total', 'cantidad' => $cantidadTotal);
		$arr['cantidades'] = $arrItemPos;

		$resultado[] = $arr;
	}

	return $resultado;
}

$idProveedor = Funciones::get('idProveedor');
$idLoteDeProduccion = Funciones::get('idLoteDeProduccion');
$productiva = Funciones::get('productiva');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');

try {
	if (empty($idProveedor)) {
		throw new FactoryExceptionCustomException('Debe especificar un proveedor');
	}

	$strFechas = Funciones::strFechas($desde, $hasta, 'fecha_alta');
	$where = 'cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' AND ';
	$where .= (empty($idLoteDeProduccion) ? '' : 'nro_lote = ' . Datos::objectToDB($idLoteDeProduccion) . ' AND ');
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'productiva = ' . Datos::objectToDB($productiva) . ' AND ';
	$where .= (empty($strFechas) ? '' : $strFechas . ' AND ');
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_alta ASC, cod_presupuesto ASC';

	$presupuestos = Factory::getInstance()->getListObject('Presupuesto', $where . $order);

	$arr = array();
	foreach ($presupuestos as $presupuesto) {
		$arr = array_merge($arr, jsonArrayDetalles($presupuesto->detalleNoSaciado));
	}

	if (count($arr) == 0) {
		throw new FactoryExceptionCustomException('No hay presupuestos a saciar con los filtros especificados');
	}

	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>