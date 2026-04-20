<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/presupuesto/manual/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$presupuesto = Factory::getInstance()->getPresupuesto($id);

	$presupuesto->proveedor->id;
	$presupuesto->detalle;

	$arrayPresupuesto = array();
	$arrayPresupuesto['idProveedor'] = $presupuesto->proveedor->id;
	$arrayPresupuesto['idLoteDeProduccion'] = $presupuesto->idLoteDeProduccion;
	$arrayPresupuesto['tipo'] = $presupuesto->productivo;
	$arrayPresupuesto['observaciones'] = $presupuesto->observaciones;
	$arrayPresupuesto['detalle'] = array();

	foreach($presupuesto->detalle as $item) {
		/** @var PresupuestoItem $item */
		$arrayItem = array();

		$arrayItem['fechaEntrega'] = $item->fechaEntrega;
		$arrayItem['idMaterial'] = array('id' => $item->colorMateriaPrima->material->id, 'nombre' => $item->colorMateriaPrima->material->nombre);
		$arrayItem['idColor'] = array('id' => $item->colorMateriaPrima->idColor, 'nombre' => $item->colorMateriaPrima->nombreColor);
		$arrayItem['cantidad'] = $item->cantidad;
		$arrayItem['usaRango'] = $item->material->usaRango;
		$arrayItem['unidadMedida'] = Factory::getInstance()->getUnidadDeMedida($item->material->idUnidadMedidaCompra)->nombre;
		$arrayItem['cantidadMinima'] = $item->material->loteMinimo;
		$arrayItem['cantidadMultiplo'] = $item->material->loteMultiplo;

		$arrItemPos = array();
		$cantidadTotal = 0;
		for($i = 1; $i < 11; $i++){
			$cantItem = $item->cantidades[$i];
			$talleItem = ($item->material->rango->posicion[$i] == ' ' || empty($item->material->rango->posicion[$i]) ? '-' : $item->material->rango->posicion[$i]);
			$cantItem = (empty($talleItem) ? '' : (empty($cantItem) ? 0 : $cantItem));
			$cantidadTotal += $item->cantidades[$i];
			$itemPos = array('talle' => $talleItem, 'cantidad' => $cantItem);
			$arrItemPos[$i] = $itemPos;
		}
		$arrItemPos[16] = array('talle' => 'Total', 'cantidad' => $cantidadTotal);
		$arrayItem['cantidades'] = $arrItemPos;

		$arrayPresupuesto['detalle'][] = $arrayItem;
	}

	Html::jsonEncode('', $arrayPresupuesto);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>