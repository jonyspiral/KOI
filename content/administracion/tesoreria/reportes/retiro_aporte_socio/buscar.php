<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/reportes/retiro_aporte_socio/buscar/')) { ?>
<?php

$operacion = Funciones::get('operacion');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');

try {
	//Armo el where
	if ($operacion == 1) {
		$where = 'tipo_documento = ' . Datos::objectToDB('AS') . ' AND ';
	} elseif ($operacion == 2) {
		$where = 'tipo_documento = ' . Datos::objectToDB('RS') . ' AND ';
	} else {
		$where = 'tipo_documento IN(' . Datos::objectToDB('AS') . ', ' . Datos::objectToDB('RS') . ') AND ';
	}

	$strFechas = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha');
	$where .= (empty($strFechas) ? '' : $strFechas . ' AND ');
	$where = trim($where, ' AND ');

	$lista = Factory::getInstance()->getArrayFromStoredProcedure('movimientos_caja_sp', '@where = ' . Datos::objectToDB($where));
	if (empty($lista)) {
		throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado');
	}

	$tabla = new HtmlTable(array(
								'cantRows'    => count($lista), 'cantCols' => 10, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'
						   ));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		  array(
			   array('content' => 'Fecha', 'dataType' => 'Center', 'width' => 7),
			   array('content' => 'Num.', 'dataType' => 'Center', 'width' => 5),
			   array('content' => 'E', 'dataType' => 'Center', 'width' => 3),
			   array('content' => 'Socio', 'width' => 6),
			   array('content' => 'Observaciones', 'width' => 24),
			   array('content' => 'En concepto de', 'width' => 23),
			   array('content' => 'Efectivo', 'dataType' => 'Moneda', 'width' => 8),
			   array('content' => 'Cheques', 'dataType' => 'Moneda', 'width' => 8),
			   array('content' => 'Transferencias', 'dataType' => 'Moneda', 'width' => 8),
			   array('content' => 'Total', 'dataType' => 'Moneda', 'width' => 8)
		  )
	);

	$totalEfectivo = 0;
	$totalCheques = 0;
	$totalTransferencias = 0;
	$totales = 0;
	for ($i = 0; $i < count($lista); $i++) {
		$fila = $lista[$i];

		if ($fila['tipo_documento'] == 'AS') {
			$aporte = Factory::getInstance()->getAporteSocio($fila['numero'], $fila['empresa']);
			$socio = $fila['de'];
			$observaciones = $aporte->observaciones;
			$enConceptoDe = $aporte->concepto;
		} else {
			$retiro = Factory::getInstance()->getRetiroSocio($fila['numero'], $fila['empresa']);
			$socio = $fila['para'];
			$observaciones = $retiro->observaciones;
			$enConceptoDe = $retiro->concepto;
		}

		if ($fila['tipo'] == 'I') {
			$efectivo = $fila['efectivo'];
			$cheques = $fila['cheques'];
			$transferencias = $fila['transferencias'];
			$total = $fila['total'];
			$rows[$i]->class .= ' indicador-verde';
		} else {
			$efectivo = -$fila['efectivo'];
			$cheques = -$fila['cheques'];
			$transferencias = -$fila['transferencias'];
			$total = -$fila['total'];
			$rows[$i]->class .= ' indicador-rojo';
		}

		$cells[$i][0]->content = Funciones::formatearFecha($fila['fecha'], 'd/m/Y');
		$cells[$i][1]->content = $fila['numero'];
		$cells[$i][2]->content = $fila['empresa'];
		$cells[$i][3]->content = $socio;
		$cells[$i][4]->content = $observaciones;
		$cells[$i][5]->content = $enConceptoDe;
		$cells[$i][6]->content = $efectivo;
		$cells[$i][7]->content = $cheques;
		$cells[$i][8]->content = $transferencias;
		$cells[$i][9]->content = $total;

		$totalEfectivo += $efectivo;
		$totalCheques += $cheques;
		$totalTransferencias += $transferencias;
		$totales += $total;
	}

	$tabla->getFootArray($foots);
	$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
	$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
	$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';

	$foots[0]->content = 'TOTALES';
	$foots[0]->class .= ' aCenter';
	$foots[0]->colspan = 6;
	$foots[6]->content = Funciones::formatearMoneda($totalEfectivo);
	$foots[7]->content = Funciones::formatearMoneda($totalCheques);
	$foots[8]->content = Funciones::formatearMoneda($totalTransferencias);
	$foots[9]->content = Funciones::formatearMoneda($totales);

	$tabla->create();

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>