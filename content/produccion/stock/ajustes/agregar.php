<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock/ajustes/agregar/')) { ?>
<?php

$ajustes = Funciones::post('ajustes');

function agruparArticulos($ajustes) {
	$ajustesAgrupados = array();

	foreach ($ajustes as $item) {
		if ($item['idAlmacen'] && $item['idArticulo'] && $item['idColor'] && $item['tipo']) {
			$combinado = $item['idAlmacen'] . '_' . $item['idArticulo'] . '_' . $item['idColor'];
			if (!$ajustesAgrupados[$combinado]) {
				$ajustesAgrupados[$combinado] = $item;
			} else {
				for ($i = 1; $i <= 10; $i++) {
					$ajustesAgrupados[$combinado]['cantidades'][$i] += Funciones::toInt($item['cantidades'][$i]);
				}
			}
		}
	}

	return $ajustesAgrupados;
}

try {
	Factory::getInstance()->beginTransaction();
	$ajustes = agruparArticulos($ajustes);
	foreach ($ajustes as $item) {
		$ajuste = Factory::getInstance()->getAjusteStock();
		$ajuste->almacen = Factory::getInstance()->getAlmacen($item['idAlmacen']);
		$ajuste->articulo = Factory::getInstance()->getArticulo($item['idArticulo']);
		$ajuste->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($item['idArticulo'], $item['idColor']);
		$ajuste->tipoMovimiento = $item['tipo'] == TiposMovimientoStock::negativo ? TiposMovimientoStock::negativo : TiposMovimientoStock::positivo;
		$ajuste->motivo = $item['motivo'];
		for ($i = 1; $i <= 10; $i++) {
			$ajuste->cantidad[$i] = $item['cantidades'][$i];
		}
		$ajuste->guardar()->notificar('produccion/stock/ajustes/agregar/');
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