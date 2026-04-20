<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/reportes/historico/buscar/')) { ?>
<?php

function crearTabla($rango) {
	$headerArray = array(
		array('content' => 'Proveedor', 'width' => 29),
		array('content' => 'Color', 'dataType' => 'Center', 'width' => 4),
		array('content' => 'Ord. compra', 'dataType' => 'Center', 'width' => 8),
		array('content' => 'F. emisión', 'dataType' => 'Fecha', 'width' => 7),
		array('content' => 'F. entrega', 'dataType' => 'Fecha', 'width' => 7)
	);

	if ($rango->id) {
		for ($j = 0; $j < 10; $j++) {
			$headerArray[] = array('content' => (empty($rango->posicion[$j + 1]) ? '-' : $rango->posicion[$j + 1]), 'dataType' => 'Entero', 'width' => 4);
		}
		$headerArray[] = array('content' => 'Total', 'dataType' => 'Entero', 'width' => 5);
		$cantidadColumnasExtra = 11;
	} else {
		$headerArray[] = array('content' => 'Cantidad', 'dataType' => 'Right', 'width' => 55);
		$cantidadColumnasExtra = 1;
	}

	$tabla = new HtmlTable(array('cantRows' => count($detalles), 'cantCols' => 5 + $cantidadColumnasExtra, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '100%',
							 	 'tdBaseClass' => 'bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'bBottomDarkGray bLeftDarkGray bRightDarkGray'));

	$tabla->createHeaderFromArray($headerArray);

	return $tabla;
}

$idMaterial = Funciones::get('idMaterial');
$idColor = Funciones::get('idColor');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');

try {
	if (empty($idMaterial)) {
		throw new FactoryExceptionCustomException('Debe especificar un material');
	}

	//Armo el where
	$strFechas = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_alta');
	
	$where = 'es_hexagono = ' . Datos::objectToDB('N') . ' AND ';
	// $where .= 'cantidad_pendiente > ' . Datos::objectToDB(ParametrosCompras::minimoParaConsiderarCumplido) . ' AND ';
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= (empty($strFechas) ? '' : $strFechas . ' AND ');
	$where .= (empty($idMaterial) ? '' : 'cod_material = ' . Datos::objectToDB($idMaterial) . ' AND ');
	$where .= (empty($idColor) ? '' : 'cod_color = ' . Datos::objectToDB($idColor) . ' AND ');
	$where = trim($where, ' AND ');
	$orderBy = ' ORDER BY cod_material, cod_proveedor, cod_color, cod_orden_de_compra';

	$detalles = Factory::getInstance()->getListObject('OrdenDeCompraItem', $where . $orderBy);
	if (empty($detalles)) {
		throw new FactoryExceptionCustomException('No existen órdenes de compra con el filtro especificado');
	}

	$html = '';
	$i = 0;
	$item = $detalles[0];
	while ($i < count($detalles)) {
		/** @var OrdenDeCompraItem $item */
		$tabla = crearTabla($item->material->rango);
		$tabla->caption = $item->material->getIdNombre();
		$tabla->captionClass ='s20';

		$item = $detalles[$i];
		$esPrimero = true;
		$totalFooter = 0;
		$totalArrayFooter = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		while ($esPrimero || ($idAnterior == $item->idMaterial)) {
			$itemActual = $item;
			$esPrimero = false;
			$idAnterior = $item->idMaterial;

			$row = new HtmlTableRow();
			for($j = 0; $j < $tabla->cantCols; $j++) {
				$cells[$j] = new HtmlTableCell();
				$cells[$j]->class = 'pRight5 pLeft5';
				if ($j == 0) {
					$cells[$j]->class .= ' bAllDarkGray';
				} else {
					$cells[$j]->class .= ' bTopDarkGray bBottomDarkGray bRightDarkGray';
				}
			}

			$cells[0]->content = $item->ordenDeCompra->proveedor->getIdNombre();
			$cells[1]->content = $item->idColorMaterial;
			$cells[2]->content = $item->idOrdenDeCompra;
			$cells[3]->content = $item->fechaAlta;
			$cells[4]->content = $item->fechaEntrega;

			if($item->material->usaRango()) {
				$total = 0;
				for ($j = 0; $j < 10; $j++) {
					$cells[5 + $j]->content = Funciones::toInt($item->cantidades[$j + 1]);
					$total += Funciones::toInt($item->cantidades[$j + 1]);
					$totalArrayFooter[$j] += Funciones::toInt($item->cantidades[$j + 1]);
				}
				$cells[15]->content = Funciones::toInt($total);
				$totalFooter += Funciones::toInt($total);
			} else {
				$cells[5]->content = Funciones::formatearDecimales($item->cantidad, 4);
				$totalFooter += $item->cantidad;
			}

			for($j = 0; $j < $tabla->cantCols; $j++) {
				$row->addCell($cells[$j]);
			}
			$tabla->addRow($row);

			$i++;
			$item = $detalles[$i];
		}
		$tabla->getFootArray($foots);
		$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
		$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
		$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';

		$foots[0]->content = 'TOTALES';
		$foots[0]->class .= ' aCenter';
		$foots[0]->colspan = 5;

		if ($itemActual->material->usaRango()) {
			for ($j = 5, $z = 0; $z < 10; $j++, $z++) {
				$foots[$j]->content = $totalArrayFooter[$z];
			}
			$foots[15]->content = $totalFooter;
		} else {
			$foots[5]->content = Funciones::formatearDecimales($totalFooter, 4);
		}

		$html .= $tabla->create();
	}

	echo $html;

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
