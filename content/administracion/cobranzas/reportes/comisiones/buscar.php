<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/reportes/comisiones/buscar/')) { ?>
<?php

$idVendedor = Funciones::get('idVendedor');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');

try {
	$vendedor = Factory::getInstance()->getVendedor($idVendedor);

	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'cod_vendedor = ' . Datos::objectToDB($vendedor->id);

	$clientesVendedor = Factory::getInstance()->getListObject('ClienteTodos', $where);

	if (empty($clientesVendedor)) {
		throw new FactoryExceptionCustomException('El vendedor seleccionado no posee clientes');
	}

	$condicionClientes = 'cod_cliente IN(';
	foreach($clientesVendedor as $cliente) {
		$condicionClientes .= $cliente->id . ',';
	}
	$condicionClientes = trim($condicionClientes, ',');
	$condicionClientes .= ')';

	$where = $condicionClientes . ' AND ';
	$where .= Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha', true, true);
	$recibos = Factory::getInstance()->getArrayFromView('subdiario_de_ingresos_v', $where . ' ORDER BY fecha ASC');

	$where = $condicionClientes . ' AND ';
	$where .= 'tipo_docum_2 = ' . Datos::objectToDB(TiposDocumento2::ndbChequeRechazado) . ' AND ';
	$where .= Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_documento', true, true);
	$notasDebitoRechazo = Factory::getInstance()->getListObject('NotaDeDebito', $where . ' ORDER BY fecha_documento ASC');

	if (empty($recibos) && empty($notasDebitoRechazo)) {
		throw new FactoryExceptionCustomException('No existen recibos ni notas de débito por rechazo de cheques para los filtros especificados');
	}

	$alicuotaComision = $vendedor->porcComisionVtas / 100;
	$cantidadFilas = count($recibos) + count($notasDebitoRechazo);

	$tabla = new HtmlTable(array('cantRows' => $cantidadFilas, 'cantCols' => 9, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		  array(
			   array('content' => 'Cliente', 'width' => 30),
			   array('content' => 'Doc.', 'dataType' => 'Center', 'width' => 5, 'title' => 'Documento'),
			   array('content' => 'Número', 'dataType' => 'Center', 'width' => 8),
			   array('content' => 'E', 'dataType' => 'Center', 'width' => 4, 'title' => 'Empresa'),
			   array('content' => 'Fecha', 'dataType' => 'Fecha', 'width' => 8),
			   array('content' => 'Efectivo', 'dataType' => 'Moneda', 'width' => 10),
			   array('content' => 'Cheques', 'dataType' => 'Moneda', 'width' => 10),
			   array('content' => 'Transf.', 'dataType' => 'Moneda', 'width' => 10, 'title' => 'Transferencias'),
			   array('content' => 'Total', 'dataType' => 'Moneda', 'width' => 15)
		  )
	);

	$i = 0;
	$total = 0;
	foreach($recibos as $recibo) {
		$esEmpresa1 = $recibo['empresa'] == '1';

		if($esEmpresa1) {
			$rows[$i]->class .= ' bold';
		}

		$cells[$i][0]->content = $recibo['de_para'];
		$cells[$i][1]->content = 'REC';
		$cells[$i][2]->content = $recibo['numero'];
		$cells[$i][3]->content = $recibo['empresa'];
		$cells[$i][4]->content = $recibo['fecha'];
		$cells[$i][5]->content = ($esEmpresa1 ? $recibo['efectivo'] / 1.21 : $recibo['efectivo']);
		$cells[$i][6]->content = ($esEmpresa1 ? $recibo['cheques'] / 1.21 : $recibo['cheques']);
		$cells[$i][7]->content = ($esEmpresa1 ? $recibo['transferencias'] / 1.21 : $recibo['transferencias']);
		$cells[$i][8]->content = $cells[$i][5]->content + $cells[$i][6]->content + $cells[$i][7]->content;

		$total += $cells[$i][8]->content;
		$i++;
	}

	foreach($notasDebitoRechazo as $ndr) {
		/** @var NotaDeDebito $ndr */
		$esEmpresa1 = $ndr->empresa == '1';

		if($esEmpresa1) {
			$rows[$i]->class .= ' bold';
		}

		$cells[$i][0]->content = '[' . $ndr->cliente->id . '] ' . $ndr->cliente->razonSocial;
		$cells[$i][1]->content = $ndr->tipoDocumento2;
		$cells[$i][2]->content = $ndr->numero;
		$cells[$i][3]->content = $ndr->empresa;
		$cells[$i][4]->content = $ndr->fecha;
		$cells[$i][5]->content = 0;
		$cells[$i][6]->content = -(($esEmpresa1 ? $ndr->importeTotal / 1.21 : $ndr->importeTotal));
		$cells[$i][7]->content = 0;
		$cells[$i][8]->content = $cells[$i][5]->content;

		$total += $cells[$i][8]->content;
		$i++;
	}

	$tabla->getFootArray($foots);
	$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
	$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
	$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';

	$foots[0]->content = 'COMISIÓN';
	$foots[0]->class .= ' aCenter';
	$foots[0]->colspan = 8;
	$foots[8]->content = Funciones::formatearMoneda($total * $alicuotaComision);

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