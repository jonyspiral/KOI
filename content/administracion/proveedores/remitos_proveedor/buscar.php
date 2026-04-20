<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/remitos_proveedor/buscar/')) { ?>
<?php

$idRemito = Funciones::get('idRemito');

try {
	$where = 'cod_remito_proveedor = ' . Datos::objectToDB($idRemito);
	$detalles = Factory::getInstance()->getListObject('RemitoPorOrdenDeCompra', $where);

	$remitoProveedor = Factory::getInstance()->getRemitoProveedor($idRemito);

	$arrayRemito = array();
	$arrayRemito['idProveedor'] = $remitoProveedor->proveedor->id;
	$arrayRemito['sucursal'] = $remitoProveedor->sucursal;
	$arrayRemito['numero'] = Funciones::toInt($remitoProveedor->numero);
	$arrayRemito['detalle'] = array();

	foreach($detalles as $item){
		/** @var RemitoPorOrdenDeCompra $item */
		$arrayItem = array();

		$arrayItem['idOrdenDeCompra'] = array('id' => $item->ordenDeCompra->id, 'nombre' => $item->ordenDeCompra->fechaAlta);
		$arrayItem['idMaterial'] = array('id' => $item->ordenDeCompraItem->colorMateriaPrima->material->id, 'nombre' => $item->ordenDeCompraItem->colorMateriaPrima->material->nombre);
		$arrayItem['idColor'] = array('id' => $item->ordenDeCompraItem->colorMateriaPrima->idColor, 'nombre' => $item->ordenDeCompraItem->colorMateriaPrima->nombreColor);
		$arrayItem['cantidad'] = $item->cantidad;
		$arrayItem['usaRango'] = $item->ordenDeCompraItem->colorMateriaPrima->material->usaRango;

		$arrItemPos = array();
		for($i = 1; $i < 11; $i++){
			$cantItem = $item->cantidades[$i];
			$talleItem =  $item->ordenDeCompraItem->colorMateriaPrima->material->rango->posicion[$i];
			$cantItem = (empty($talleItem) ? '' : (empty($cantItem) ? 0 : $cantItem));
			$itemPos = array('talle' => $talleItem, 'cantidad' => $cantItem);
			$arrItemPos[$i] = $itemPos;
		}
		$arrItemPos[11] = array('talle' => 'Total', 'cantidad' => $ordenCompraItem->cantidadPendiente);
		$arrayItem['cantidades'] = $arrItemPos;

		$arrayRemito['detalle'][] = $arrayItem;
	}

	Html::jsonEncode('', $arrayRemito);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>