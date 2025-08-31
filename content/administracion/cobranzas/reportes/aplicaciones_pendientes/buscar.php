<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/reportes/aplicaciones_pendientes/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$empresa = Funciones::get('empresa');
$arrayDocumentosIncluidos = array(
	TiposDocumento::factura => (Funciones::get('checkboxFAC') == 'S'),
	TiposDocumento::notaDeDebito => (Funciones::get('checkboxNDB') == 'S'),
	TiposDocumento::notaDeCredito => (Funciones::get('checkboxNCR') == 'S'),
	TiposDocumento::recibo => (Funciones::get('checkboxREC') == 'S')
);
$ordenadoPor = Funciones::get('ordenadoPor');
$esXls = Funciones::get('esXls') == '1';

try {
	$arrayOrderBy = array(
		'0' => 'fecha_documento ASC',
		'1' => 'fecha_documento DESC',
		'2' => 'cod_cliente ASC'
	);

	$strFecha = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_documento');

	$where = '';
	$where .= (empty($empresa) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ');
	$where .= (empty($idCliente) ? '' : 'cod_cliente = ' . Datos::objectToDB($idCliente) . ' AND ');
	$where .= (empty($strFecha) ? '' : $strFecha . ' AND ');

	$incluyeAlMenosUno = false;
	$incluye = 'tipo_docum IN(';
	foreach ($arrayDocumentosIncluidos as $tipoDocumento => $incluido) {
		$incluyeAlMenosUno = true;
		if ($incluido) {
			$incluye .= Datos::objectToDB($tipoDocumento) . ', ';
		}
	}

	if (!$incluyeAlMenosUno) {
		throw new FactoryExceptionCustomException('Debe seleccionar al menos un tipo de documento');
	}

	$where .= trim($incluye, ', ') . ') AND ';
	$where = trim($where, ' AND ');

	$orderBy = (empty($arrayOrderBy[$ordenadoPor]) ? '' : ' ORDER BY ' . $arrayOrderBy[$ordenadoPor]);

	$pendientes = Factory::getInstance()->getArrayFromView('pendientes_aplicacion_clientes_v', $where . $orderBy);

	$cantidadFilas = count($pendientes);
	if ($cantidadFilas == 0) {
		throw new FactoryExceptionCustomException('No existen documentos con los filtros especificados');
	}

	$tabla = new HtmlTable(array('cantRows' => $cantidadFilas, 'cantCols' => 9, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								 'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		  array(
			   array('content' => 'Cliente', 'width' => 13),
			   array('content' => 'Fecha', 'dataType' => 'Fecha', 'width' => 8),
			   array('content' => 'F. vto.', 'dataType' => 'Center', 'width' => 8, 'title' => 'Fecha vencimiento'),
			   array('content' => 'Tipo<br>doc.', 'dataType' => 'Center', 'width' => 5, 'title' => 'Tipo documento'),
			   array('content' => 'Letra', 'dataType' => 'Center', 'width' => 5),
			   array('content' => 'Número', 'dataType' => 'Center', 'width' => 5),
			   array('content' => 'Observaciones', 'width' => 40),
			   array('content' => 'Imp. tot.', 'dataType' => 'Moneda', 'width' => 8, 'title' => 'Importe total'),
			   array('content' => 'Imp. pend.', 'dataType' => 'Moneda', 'width' => 8, 'title' => 'Importe pendiente')
		  )
	);

	$i = 0;
	$total = 0;
	$totalPendiente = 0;
	foreach($pendientes as $pendiente) {
		if($pendiente['empresa'] == '1') {
			$rows[$i]->class .= ' bold';
		}

		$cliente = (empty($pendiente['cod_cliente']) ? $pendiente['razon_social'] : '[' . $pendiente['cod_cliente'] . ']' . $pendiente['razon_social']);

		$cells[$i][0]->content = ($esXls ? $cliente : Funciones::acortar($cliente, 13, '..'));
		$cells[$i][0]->title = $cliente;
		$cells[$i][1]->content = $pendiente['fecha_documento'];
		$cells[$i][2]->content = (empty($pendiente['fecha_vencimiento']) ? '-' : Funciones::formatearFecha($pendiente['fecha_vencimiento']));
		$cells[$i][3]->content = $pendiente['tipo_docum'];
		$cells[$i][4]->content = $pendiente['letra'];
		$cells[$i][5]->content = $pendiente['nro_documento'];
		$cells[$i][6]->content = $pendiente['observaciones'];
		$cells[$i][7]->content = $pendiente['importe_total'];
		$cells[$i][8]->content = $pendiente['importe_pendiente'];

		$total += $pendiente['importe_total'];
		$totalPendiente += $pendiente['importe_pendiente'];

		$i++;
	}

	$tabla->getFootArray($foots);
	$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
	$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
	$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';

	$foots[0]->content = 'TOTAL';
	$foots[0]->class .= ' aCenter';
	$foots[0]->colspan = 7;
	$foots[7]->content = Funciones::formatearMoneda($total);
	$foots[8]->content = Funciones::formatearMoneda($totalPendiente);

	$tabla->create();
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}




?>
<?php } ?>