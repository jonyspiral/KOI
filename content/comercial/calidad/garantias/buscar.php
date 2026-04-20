<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/calidad/garantias/buscar/')) { ?>
<?php

function ampliarEcommerce($ecommerce) {
	/** @var Ecommerce_Order[] $ecommerce */
	foreach ($ecommerce as $o) {
		$o->expand();
	}
	return $ecommerce;
}

function ampliarGarantias($garantias) {
	/** @var Garantia[] $garantias */
	foreach ($garantias as $g) {
		if ($g->idCliente) {
			$g->cliente;
		} elseif ($g->idOrder) {
			$g->order->expand();
		}
		foreach ($g->detalle as $key => $val) {
			$g->detalle[$key]->almacen->expand();
			$g->detalle[$key]->articulo->expand();
			$g->detalle[$key]->colorPorArticulo->expand();
			$g->detalle[$key]->expand();
		}
		$g->expand();
	}
	return $garantias;
}

try {
	$where = 'cod_status IN (' . Datos::objectToDB(Ecommerce_OrderStatus_PendienteDeCambio::STATUS_ID) . ', ' . Datos::objectToDB(Ecommerce_OrderStatus_PendienteDeDevolucion::STATUS_ID) . ') AND anulado = ' . Datos::objectToDB('N');
	$ecommerce = Factory::getInstance()->getListObject('Ecommerce_Order', $where);

	$garantias = Factory::getInstance()->getListObject('Garantia', 'solucion_ncr IS NULL AND devuelta = ' . Datos::objectToDB('N') . ' AND anulado = ' . Datos::objectToDB('N'));
	Html::jsonEncode('', array('ecommerce' => ampliarEcommerce($ecommerce), 'garantias' => ampliarGarantias($garantias)));
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar buscar las garantías. ' . $ex->getMessage());
}

?>
<?php } ?>