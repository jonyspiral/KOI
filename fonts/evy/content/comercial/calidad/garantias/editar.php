<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/calidad/garantias/editar/')) { ?>
<?php

$idGarantia = Funciones::post('idGarantia');
$items = Funciones::post('items');

try {
	Factory::getInstance()->beginTransaction();

	if (!$idGarantia || !count($items)) {
		throw new FactoryExceptionCustomException('No se recibió correctamente el ID de la garantía o la lista de items');
	}

	/** @var Garantia $garantia */
	$garantia = Factory::getInstance()->getGarantia($idGarantia);
	if ($garantia->clasificada == 'S') {
		throw new FactoryExceptionCustomException('La garantía ya fue clasificada. Por favor recargue la página');
	}
	if ($garantia->devuelta == 'S') {
		throw new FactoryExceptionCustomException('La garantía ya fue devuelta. Por favor recargue la página');
	}

	//Creo un array y un total de las cantidades de la garantía para efectivamente comprobar luego dos cosas: 1) Que no se pasen en las cantidades por talle 2) Que clasifiquen TODA la garantía
	$totalPares = 0;
	$arrayCantidades = array();
	foreach ($garantia->detalle as $item) {
		$arrayCantidades[$item->id] = array('item' => $item, 'cantidades' => array());
		for ($i = 1; $i <= 10; $i++) {
			$totalPares += $item->cantidad[$i];
			$arrayCantidades[$item->id]['cantidades'][$i] = $item->cantidad[$i];
		}
	}

	//Empiezo a generar el JSON con los detalles de los movimientos de almacén
	$garantia->movimientos = array();
	foreach ($items as $item) {
		foreach ($item['detalle'] as $detalle) {
			if (!$detalle['idAlmacen']) {
				throw new FactoryExceptionCustomException('Falta ingresar el almacén al que quiere mover la mercadería en alguna de las líneas');
			}

			$movimiento = array(
				'idAlmacenOrigen'		=> $arrayCantidades[$item['idItem']]['item']->idAlmacen,
				'idAlmacenDestino'		=> $detalle['idAlmacen'],
				'idArticulo'			=> $arrayCantidades[$item['idItem']]['item']->idArticulo,
				'idColorPorArticulo'	=> $arrayCantidades[$item['idItem']]['item']->idColorPorArticulo,
				'cantidad'				=> array()
			);
			for ($i = 1; $i <= 10; $i++) {
				$movimiento['cantidad'][$i] = Funciones::toInt(Funciones::keyIsSet($detalle, $i, 0));

				//Descuento del array de cantidades
				if (($arrayCantidades[$item['idItem']]['cantidades'][$i] -= $movimiento['cantidad'][$i]) < 0) {
					$articulo = $arrayCantidades[$item['idItem']]['item']->articulo->nombre . ' - ' . $arrayCantidades[$item['idItem']]['item']->colorPorArticulo->nombre;
					throw new FactoryExceptionCustomException('Ha intentado clasificar más pares de los que puede ingresar en el artículo ' . $articulo . ' de la garantía');
				}

				//Descuento del total
				$totalPares -= $movimiento['cantidad'][$i];
			}

			if (Funciones::sumaArray($movimiento['cantidad'])) {
				$garantia->movimientos[] = $movimiento;
			}
		}
	}

	if ($totalPares != 0) {
		//throw new FactoryExceptionCustomException('No se han clasificado todos los pares de la garantía. Ésta debería estar completa para poder continuar con la clasificación');
		/** @var Garantia $garantia */
		$garantiaDerivada = Factory::getInstance()->getGarantia();
		$garantiaDerivada->observaciones = $garantia->observaciones;

		if ($garantia->esEcommerce()) {
			$garantiaDerivada->order = $garantia->order;
		} else {
			$garantiaDerivada->cliente = $garantia->cliente;
		}

		$importeTotal = 0;
		foreach ($arrayCantidades as $obj) {
			if (Funciones::sumaArray($obj['cantidades']) > 0) {
				$garantiaItem = Factory::getInstance()->getGarantiaItem();
				$garantiaItem->almacen = $obj['item']->almacen;
				$garantiaItem->articulo = $obj['item']->articulo;
				$garantiaItem->colorPorArticulo = $obj['item']->colorPorArticulo;

				$importeUnitario = $obj['item']->importeNcr / $obj['item']->cantidadTotal;
				$garantiaItem->cantidad = $obj['cantidades'];
				$garantiaItem->importeNcr = $importeUnitario * $garantiaItem->cantidadTotal;
				$importeTotal += $garantiaItem->importeNcr;

				$garantiaDerivada->addItem($garantiaItem);

				for ($i = 1; $i <= 10; $i++) {
					$obj['item']->cantidad[$i] -= $obj['cantidades'][$i];
				}
				$obj['item']->cantidadTotal = Funciones::sumaArray($obj['item']->cantidad);
				$obj['item']->importeNcr -= $garantiaItem->importeNcr;
				$obj['item']->guardar();
			}
		}

		$garantiaDerivada->totalNcr = $importeTotal;
		$garantiaDerivada->derivada = $garantia->id;

		$garantiaDerivada->guardar();

		$garantia->totalNcr -= $importeTotal;
	}

	//Mando a guardar
	$garantia->clasificada = 'S';
	$garantia->guardar()->notificar('comercial/calidad/garantias/' . (isset($garantia->idOrder) ? 'ecommerce/' : 'normal/') . 'editar/');

	Factory::getInstance()->commitTransaction();
	Html::jsonSuccess('Se guardó correctamente la garantía');
} catch (FactoryExceptionCustomException $ex) {
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar guardar la garantía');
}

?>
<?php } ?>