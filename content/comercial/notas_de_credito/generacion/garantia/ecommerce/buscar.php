<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/garantia/ecommerce/buscar/')) { ?>
<?php

try {
	$where = 'cod_order IS NOT NULL AND solucion_ncr IS NULL AND clasificada = ' . Datos::objectToDB('S') . ' AND anulado = ' . Datos::objectToDB('N');
	$order = ' ORDER BY fecha_ultima_mod DESC';
	$garantias = Factory::getInstance()->getListObject('Garantia', $where . $order);

	if (!count($garantias)) {
		throw new FactoryExceptionCustomException('No hay garantías pendientes');
	}

	foreach ($garantias as $garantia) {
		/** @var Garantia $garantia */
		foreach ($garantia->detalle as $detalle) {
			$detalle->expand();
			$detalle->articulo->rangoTalle;
		}
		$garantia->expand();
		$garantia->order->expand();
	}
	Html::jsonEncode('', $garantias);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>