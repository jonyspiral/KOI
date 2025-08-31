<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/reportes_gerenciales/ventas/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$empresa = Funciones::get('empresa');

try {
	//Armo el where
	$strFechas = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha');

	$where = 'precio_unitario_final != 0 AND pares != 0 AND ';
	$where .= (empty($empresa) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ');
	$where .= (empty($strFechas) ? '' : $strFechas . ' AND ');
	$where = trim($where, ' AND ');
	$where = (empty($where) ? '1=1' : $where);
	$groupBy = ' GROUP BY cod_articulo, cod_color_articulo, denom_articulo';
	$orderBy = ' ORDER BY cod_articulo';

	$listaArticulos = Factory::getInstance()->getArrayFromView('reporte_articulos_v', $where . $groupBy . $orderBy, 0,
															   'cod_articulo, cod_color_articulo, denom_articulo, AVG(ABS(precio_unitario_final)) precio_unitario_promedio,
																SUM((CASE WHEN pares > 0 THEN pares ELSE 0 END)) unidades_vendidas,
																SUM((CASE WHEN pares > 0 THEN pares * precio_unitario_final ELSE 0 END)) saldo_debe,
																-SUM((CASE WHEN pares > 0 THEN 0 ELSE pares END)) unidades_devolucion,
																-SUM((CASE WHEN pares > 0 THEN 0 ELSE pares * precio_unitario_final END)) saldo_haber,
																SUM(pares) pares_neto,
																SUM((CASE WHEN pares < 0 THEN -1 ELSE 1 END) * (pares * precio_unitario_final)) saldo_neto');

	if(empty($listaArticulos)) {
		throw new FactoryExceptionCustomException('No existen resultados para los filtros seleccionados');
	}

	$tabla = new HtmlTable(array('cantRows' => count($listaArticulos), 'cantCols' => 10, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								 'tdBaseClass' => 'bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'bBottomDarkGray bLeftDarkGray bRightDarkGray'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'Cod. art.', 'dataType' => 'Center', 'width' => 6, 'title' => 'Código artículo'),
			 array('content' => 'Color', 'dataType' => 'Center', 'width' => 5),
			 array('content' => 'Descripción', 'width' => 20),
			 array('content' => 'Precio unit. prom.', 'dataType' => 'Moneda', 'width' => 13, 'title' => 'Precio unitario promedio'),
			 array('content' => 'U. vendidas', 'dataType' => 'Center', 'width' => 8, 'title' => 'Unidades vendidas'),
			 array('content' => 'Total debe', 'dataType' => 'Moneda', 'width' => 10, 'title' => 'Total debe'),
			 array('content' => 'U. devolución', 'dataType' => 'Center', 'width' => 8, 'title' => 'Unidades devolución'),
			 array('content' => 'Total haber', 'dataType' => 'Moneda', 'width' => 10, 'title' => 'Total haber'),
			 array('content' => 'Neto Pares', 'dataType' => 'Center', 'width' => 10),
			 array('content' => 'Importe Neto', 'dataType' => 'Moneda', 'width' => 10)
		)
	);

	$totales = array(
		'unidades_vendidas' => 0,
		'total_debe' => 0,
		'unidades_devolucion' => 0,
		'total_haber' => 0,
		'total_pares' => 0,
		'importe_total' => 0
	);

	for ($i = 0; $i < count($listaArticulos); $i++) {
		$articulo = $listaArticulos[$i];

		$totales['unidades_vendidas'] += $articulo['unidades_vendidas'];
		$totales['total_debe'] += $articulo['saldo_debe'];
		$totales['unidades_devolucion'] += $articulo['unidades_devolucion'];
		$totales['total_haber'] += $articulo['saldo_haber'];
		$totales['total_pares'] += $articulo['pares_neto'];
		$totales['importe_total'] += $articulo['saldo_neto'];

		$cells[$i][0]->content = $articulo['cod_articulo'];
		$cells[$i][1]->content = $articulo['cod_color_articulo'];
		$cells[$i][2]->content = $articulo['denom_articulo'];
		$cells[$i][3]->content = $articulo['precio_unitario_promedio'];
		$cells[$i][4]->content = $articulo['unidades_vendidas'];
		$cells[$i][5]->content = $articulo['saldo_debe'];
		$cells[$i][6]->content = $articulo['unidades_devolucion'];
		$cells[$i][7]->content = $articulo['saldo_haber'];
		$cells[$i][8]->content = $articulo['pares_neto'];
		$cells[$i][9]->content = $articulo['saldo_neto'];
	}

	$tabla->getFootArray($foots);
	$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
	$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
	$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';

	$foots[0]->content = 'TOTALES';
	$foots[0]->class .= ' aCenter';
	$foots[0]->colspan = 4;
	$foots[4]->content = $totales['unidades_vendidas'];
	$foots[5]->content = Funciones::formatearMoneda($totales['total_debe']);
	$foots[6]->content = $totales['unidades_devolucion'];
	$foots[7]->content = Funciones::formatearMoneda($totales['total_haber']);
	$foots[8]->content = $totales['total_pares'];
	$foots[9]->content = Funciones::formatearMoneda($totales['importe_total']);

	echo $tabla->create(true);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
