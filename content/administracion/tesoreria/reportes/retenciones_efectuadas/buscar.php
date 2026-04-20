<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/reportes/retenciones_efectuadas/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');

try {
	//Armo el where
	$where = '';
	$where .= Funciones::strFechas($fechaDesde,$fechaHasta, 'fecha');
	$where = (empty($where) ? '1=1' : $where);
	$orderBy = ' ORDER BY fecha ASC';

	$lista = Factory::getInstance()->getArrayFromView('retenciones_efectuadas_v', $where . $orderBy);
	if(empty($lista)) {
		throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado');
	}

	$tabla = new HtmlTable(array('cantRows' => count($lista), 'cantCols' => 6, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'Fecha', 'dataType' => 'Center', 'width' => 10),
			 array('content' => 'Proveedor', 'width' => 40),
			 array('content' => 'Cuit.', 'dataType' => 'Center', 'width' => 10),
			 array('content' => 'Importe', 'dataType' => 'Moneda', 'width' => 15),
			 array('content' => 'Nº OP', 'dataType' => 'Center', 'width' => 10),
			 array('content' => 'Importe OP', 'dataType' => 'Moneda', 'width' => 15)
		)
	);

	$totalRetenciones = 0;
	$totalOp = 0;
	for ($i = 0; $i < count($lista); $i++) {
		$fila = $lista[$i];

		for ($j = 0; $j < $tabla->cantCols; $j++) {
			if ($j == 0) $cells[$i][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
			else $cells[$i][$j]->class .= ' bBottomDarkGray';
			if ($j == 6) $cells[$i][$j]->class .= ' bRightDarkGray bBottomDarkGray';
		}

		$cells[$i][0]->content = Funciones::formatearFecha($fila['fecha'], 'd/m/Y');
		$cells[$i][1]->content = $fila['razon_social'];
		$cells[$i][2]->content = Funciones::ponerGuionesAlCuit($fila['cuit']);
		$cells[$i][3]->content = $fila['importe_retencion'];
		$cells[$i][4]->content = $fila['nro_orden_de_pago'];
		$cells[$i][5]->content = $fila['importe_orden_de_pago'];

		$totalRetenciones += $fila['importe_retencion'];
		$totalOp += $fila['importe_orden_de_pago'];
	}

	$tabla->getFootArray($foots);
	$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
	$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
	$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';

	$foots[0]->content = 'TOTALES';
	$foots[0]->class .= ' aCenter';
	$foots[0]->colspan = 3;
	$foots[3]->content = Funciones::formatearMoneda($totalRetenciones);
	$foots[4]->content = '-';
	$foots[4]->class .= ' aCenter';
	$foots[5]->content = Funciones::formatearMoneda($totalOp);

	$tabla->create();

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>