<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/remitos_proveedor/buscar/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');

try {
	if (empty($idProveedor))
		throw new FactoryExceptionCustomException('El proveedor no existe');

	$proveedor = Factory::getInstance()->getProveedor($idProveedor);

	$where = 'cantidad_pendiente > ' . Datos::objectToDB(ParametrosCompras::minimoParaConsiderarCumplido) . ' AND ';
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'es_hexagono = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'cod_proveedor = ' . Datos::objectToDB($proveedor->id);
	$orderBy = ' ORDER BY fecha_emision ASC;';
	$ordenesCompraItem = Factory::getInstance()->getListObject('OrdenDeCompraItem', $where . $orderBy);

	if (count($ordenesCompraItem) == 0)
		throw new FactoryExceptionCustomException('No existen ordenes de compra pendientes para el proveedor Nº "' . $proveedor->id . '"');

	$resultado = array();
	foreach($ordenesCompraItem as $ordenCompraItem){
		/** @var OrdenDeCompraItem $ordenCompraItem */
		$array = array();
		$array['talleUnico'] = 'S';
		$array['numero'] = (is_null($ordenCompraItem->ordenDeCompra->numero) ? '-' : $ordenCompraItem->ordenDeCompra->numero);
		$array['fechaEntrega'] = (is_null($ordenCompraItem->fechaEntrega) ? '-' : $ordenCompraItem->fechaEntrega);
		$array['material'] = $ordenCompraItem->material->getIdNombre();
		$array['color'] = $ordenCompraItem->colorMateriaPrima->idColor;
		$array['objOrdenDeCompra'] = array('id' => $ordenCompraItem->ordenDeCompra->id, 'nombre' => $ordenCompraItem->ordenDeCompra->fechaAlta);
		$array['objMaterial'] = array('id' => $ordenCompraItem->material->id, 'nombre' => $ordenCompraItem->material->getIdNombre());
		$array['objColor'] = array('id' => $ordenCompraItem->colorMateriaPrima->idColor, 'nombre' => $ordenCompraItem->colorMateriaPrima->idColor . ' - ' . $ordenCompraItem->colorMateriaPrima->nombreColor);
		$array['cantidad'] = Funciones::formatearDecimales($ordenCompraItem->cantidadPendiente, 4);
		if($ordenCompraItem->material->usaRango == 'S'){
			$array['talleUnico'] = 'N';
			$arrItemPos = array();
			for($i = 1; $i < 16; $i++){
				$cantItem = $ordenCompraItem->cantidadesPendientes[$i];
				$talleItem = $ordenCompraItem->material->rango->posicion[$i];
				$cantItem = (empty($talleItem) ? '' : (empty($cantItem) ? 0 : $cantItem));
				$talleItem = (empty($talleItem) ? '---' : $talleItem);
				$itemPos = array('talle' => $talleItem, 'cantidad' => $cantItem);
				$arrItemPos[$i] = $itemPos;
			}
			$arrItemPos[16] = array('talle' => 'Total', 'cantidad' => $ordenCompraItem->cantidadPendiente);
			$array['cantidades'] = $arrItemPos;
		}
		$resultado[] = $array;
	}

	Html::jsonEncode('', $resultado);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>