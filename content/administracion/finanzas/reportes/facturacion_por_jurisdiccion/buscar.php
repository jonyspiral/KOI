<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/finanzas/reportes/facturacion_por_jurisdiccion/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$empresa = Funciones::get('empresa');
$orderBy = Funciones::get('orderBy');

function armoHeadTablaDetallada(HtmlTable &$tabla) {
	$tabla->getHeadArray($ths);
	$widths = array(20, 10, 15, 20, 15, 20);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$ths[$i]->style->width = $widths[$i] . '%';
		if ($i == 0) $ths[$i]->class = 'cornerL5';
		elseif ($i == $tabla->cantCols - 1) $ths[$i]->class = 'cornerR5 bLeftWhite';
		else $ths[$i]->class = 'bLeftWhite';
	}
	$tabla->headerClass('tableHeader');
	$ths[0]->content = 'Provincia';
	$ths[1]->content = 'Pares';
	$ths[1]->dataType = 'Entero';
	$ths[2]->content = 'Neto';
	$ths[2]->dataType = 'Moneda';
	$ths[3]->content = 'Iva';
	$ths[3]->dataType = 'Moneda';
	$ths[4]->content = 'Descuento';
	$ths[4]->dataType = 'Moneda';
	$ths[5]->content = 'Total';
	$ths[5]->dataType = 'Moneda';
	return $tabla;
}

try {
	//Inicializo totales
	$totalPares = 0;
	$totalNeto = 0;
	$totalIva = 0;
	$totalDescuento = 0;
	$total = 0;

	//Armo el where
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha', true, true) . ' AND ';
	$where .= ($empresa != 1 && $empresa != 2) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where = rtrim($where, ' AND ');
	$where .= ' GROUP BY cod_provincia, provincia';
	$where .= (empty($orderBy) ? '' : ' ORDER BY ' . $orderBy . ' DESC');

	$array[] = 'cod_provincia';
	$array[] = 'provincia';
	$array[] = 'SUM(pares) as pares';
	$array[] = 'SUM(neto) as neto';
	$array[] = 'SUM(iva) as iva';
	$array[] = 'SUM(descuento) as descuento';
	$array[] = 'SUM(total) as total';

	$listaFacProvincias = Factory::getInstance()->getArrayFromView('reporte_facturacion_v', $where, 0, $array);
	if(empty($listaFacProvincias)) {
		throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado');
	}

	$tabla = new HtmlTable(array('cantRows' => count($listaFacProvincias) + 1, 'cantCols' => 6, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tabla->getRowCellArray($rows, $cells);

	armoHeadTablaDetallada($tabla);

	for ($i = 0; $i < count($listaFacProvincias); $i++) {
		$doc = $listaFacProvincias[$i];
		$doc['pares'] = ($doc['tipo_documento'] == 'NDB' ? 0 : $doc['pares']);

		for ($j = 0; $j < $tabla->cantCols; $j++) {
			if ($j == 0) $cells[$i][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
			else $cells[$i][$j]->class .= ' bBottomDarkGray';
			if ($j == 5) $cells[$i][$j]->class .= ' bRightDarkGray bBottomDarkGray';
		}

		$cells[$i][0]->content = '[' . $doc['cod_provincia'] . '] ' . $doc['provincia'];
		$cells[$i][1]->content = $doc['pares'];
		$cells[$i][2]->content = $doc['neto'];
		$cells[$i][3]->content = $doc['iva'];
		$cells[$i][4]->content = $doc['descuento'];
		$cells[$i][5]->content = $doc['total'];

		$totalPares += $doc['pares'];
		$totalNeto += $doc['neto'];
		$totalIva += $doc['iva'];
		$totalDescuento += $doc['descuento'];
		$total += $doc['total'];
	}

	for ($j = 0; $j < $tabla->cantCols; $j++) {
		$cells[$i][$j]->class = 'bLightOrange w70 bold bBottomDarkGray bold';
	}

	$cells[$i][0]->content = 'Totales:';
	$cells[$i][1]->content = $totalPares;
	$cells[$i][2]->content = $totalNeto;
	$cells[$i][3]->content = $totalIva;
	$cells[$i][4]->content = $totalDescuento;
	$cells[$i][5]->content = $total;

	$tabla->create();

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
