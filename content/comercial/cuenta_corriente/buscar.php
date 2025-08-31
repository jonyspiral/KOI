<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('comercial/cuenta_corriente/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$empresa = Funciones::get('empresa');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');

try {
	if (!$idCliente) {
		throw new FactoryExceptionCustomException('Debe elegir un cliente');
	}
	$cc = Factory::getInstance()->getCuentaCorrienteHistorica($idCliente, $empresa);
	$cc->fechaDesde = $desde;
	$cc->fechaHasta = $hasta;
	if (count($cc->documentosPorFecha) == 0) {
		throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado');
	}

	$tieneSaldoInicial = !!$cc->fechaDesde;

	$tabla = new HtmlTable(array('cantRows' => (count($cc->documentosPorFecha) + ($tieneSaldoInicial ? 1 : 0)), 'cantCols' => 10, 'id' => 'tablaDatos', 'class' => 'bBottomDarkGray', 'cellSpacing' => 0, 'width' => '99%',
								'tdBaseClass' => 'pRight10 pLeft10 bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bLeftDarkGray bRightDarkGray'));
	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'Fecha', 'dataType' => 'Center', 'width' => 9),
			 array('content' => 'Tipo doc.', 'dataType' => 'Center', 'width' => 6),
			 array('content' => 'F. vto.', 'dataType' => 'Center', 'width' => 9, 'title' => 'Fecha de vencimiento'),
			 array('content' => 'Letra', 'dataType' => 'Center', 'width' => 3),
			 array('content' => 'Número', 'dataType' => 'Entero', 'width' => 4),
			 array('content' => 'D.P.P.', 'dataType' => 'Center', 'width' => 4, 'title' => 'Días promedio de pago'),
			 array('content' => 'Detalle', 'width' => 35),
			 array('content' => 'Debe', 'dataType' => 'Moneda', 'width' => 10),
			 array('content' => 'Haber', 'dataType' => 'Moneda', 'width' => 10),
			 array('content' => 'Saldo', 'dataType' => 'Moneda', 'width' => 10)
		)
	);
	$tabla->getRowCellArray($rows, $cells);
	$saldo = 0;
	$totalEmpresa1 = 0;
	$totalEmpresa2 = 0;

	$j = 0;
	if($tieneSaldoInicial){
		$rows[0]->class = 'bDarkOrange white';

		$arraySaldos = $cc->getSaldosIniciales();

		$totalEmpresa1 = $arraySaldos['saldo1'];
		$totalEmpresa2 = $arraySaldos['saldo2'];

		$cells[0][0]->colspan = 9;
		$cells[0][0]->content = 'SALDO A FECHA ' . Funciones::sumarTiempo($cc->fechaDesde, -1);
		$cells[0][0]->class .= ' aCenter bold';
		$saldo = $totalEmpresa1 + $totalEmpresa2;
		$cells[0][9]->content = Funciones::formatearMoneda($saldo);

		$j++;
	}

	for ($i = 0; $i < $tabla->cantRows; $i++) {
		/** @var Documento $doc */
		$doc = $cc->documentosPorFecha[$i];
		$debe = 0;
		$haber = 0;

		$importeTotal = Funciones::toFloat($doc->importeTotal);

		if($importeTotal > 0){
			$debe = $importeTotal;
		} else {
			$haber = $importeTotal * -1;
		}

		$saldo += $importeTotal;

		if ($doc->empresa == '1') {
			$diasCondPago = 15;
		} else {
			$diasCondPago = 15;
		}

		$rows[$i]->id = $doc->numero;
		$cells[$j][0]->content = $doc->fecha;
		$cells[$j][1]->content = $doc->tipoDocumento;
		$cells[$j][2]->content = Funciones::sumarTiempo($doc->fecha, $diasCondPago);
		$cells[$j][3]->content = $doc->letra;
		$cells[$j][4]->content = $doc->numero;
		$cells[$j][4]->class  .= ($doc->tipoDocumento == TiposDocumento::recibo ? ' cPointer recibo' : '');
		$cells[$j][4]->title = 'Ir a recibo ' . $doc->numero;
		$cells[$j][5]->content = $doc->diasPromedioPago;
		$cells[$j][6]->content = $doc->detalle;
		$cells[$j][7]->content = $debe;
		$cells[$j][8]->content = $haber;
		$cells[$j][9]->content = $saldo;
		if ($doc->empresa == 1){
			$rows[$j]->class .= ' bold';
			$totalEmpresa1 += $doc->importeTotal;
		} else {
			$totalEmpresa2 += $doc->importeTotal;
		}
		$j++;
	}
	$tablaTotales = new HtmlTable(array('cantRows' => 2, 'cantCols' => 1, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tablaTotales->getRowCellArray($rows, $cells);
	$cells[0][0]->content ='</br><label>Saldo: ' . Funciones::formatearMoneda($totalEmpresa1) . '</label>';
	$rows[0]->class = 'bold' . ($empresa == '2' ? ' hidden' : '');
	$cells[1][0]->content ='</br><label>Saldo: ' . Funciones::formatearMoneda($totalEmpresa2) . '</label>';
	$rows[1]->class = ($empresa == '1' ? ' hidden' : '');

	$htmlTablaTotales = $tablaTotales->create(true);
	$html = $tabla->create(true);

	echo $html . $htmlTablaTotales;
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>