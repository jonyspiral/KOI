<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock_mp/ajustes/agregar/')) { ?>
<?php

$ajustes = Funciones::post('ajustes');

function agruparMateriales($ajustes) {
	$ajustesAgrupados = array();

	foreach ($ajustes as $item) {
		if ($item['idAlmacen'] && $item['idMaterial'] && $item['idColor'] && $item['tipo']) {
			$combinado = $item['idAlmacen'] . '_' . $item['idMaterial'] . '_' . $item['idColor'];
			if (!$ajustesAgrupados[$combinado]) {
				$ajustesAgrupados[$combinado] = $item;
			} else {
				for ($i = 1; $i <= 10; $i++) {
					$ajustesAgrupados[$combinado]['cantidades'][$i] += Funciones::toFloat($item['cantidades'][$i]);
				}
			}
		}
	}

	return $ajustesAgrupados;
}

try {
	Factory::getInstance()->beginTransaction();
	$ajustes = agruparMateriales($ajustes);
	foreach ($ajustes as $item) {
		$ajuste = Factory::getInstance()->getAjusteStockMP();
		$ajuste->almacen = Factory::getInstance()->getAlmacen($item['idAlmacen']);
		$ajuste->material = Factory::getInstance()->getMaterial($item['idMaterial']);
		$ajuste->colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($item['idMaterial'], $item['idColor']);
		$ajuste->tipoMovimiento = ($item['tipo'] == TiposMovimientoStock::negativo ? TiposMovimientoStock::negativo : TiposMovimientoStock::positivo);
		$ajuste->motivo = $item['motivo'];
		for ($i = 1; $i <= 10; $i++) {
			$ajuste->cantidad[$i] = $item['cantidades'][$i];
		}
		$ajuste->guardar()->notificar('produccion/stock_mp/ajustes/agregar/');
	}
	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Se guardó correctamente el ajuste de stock');
} catch (FactoryExceptionCustomException $ex) {
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar guardar alguno de los ajustes de stock');
}

?>
<?php } ?>