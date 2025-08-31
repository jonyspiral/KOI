<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/cuenta_corriente_proveedor/buscar/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');
$empresa = Funciones::get('empresa') ? Funciones::get('empresa') : '';
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');

try {
	if (!$idProveedor) {
		throw new FactoryExceptionCustomException('Debe elegir un proveedor');
	}

	$cc = Factory::getInstance()->getCuentaCorrienteHistoricaProveedor($idProveedor, $empresa);
	$cc->fechaDesde = $desde;
	$cc->fechaHasta = $hasta;
	if(count($cc->documentosPorFecha) == 0)
		throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado');

	$tieneSaldoInicial = !!$cc->fechaDesde;

	$tabla = new HtmlTable(array('cantRows' => (count($cc->documentosPorFecha) + ($tieneSaldoInicial ? 1 : 0)), 'cantCols' => 8, 'id' => 'tablaDatos', 'class' => 'bBottomDarkGray', 'cellSpacing' => 0, 'width' => '100%',
								'tdBaseClass' => 'pRight10 pLeft10 bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bLeftDarkGray bRightDarkGray'));
	$tabla->createHeaderFromArray(
		  array(
			   array('content' => 'Fecha', 'dataType' => 'Center', 'width' => 10),
			   array('content' => 'Tipo doc.', 'dataType' => 'Center', 'width' => 10),
			   array('content' => 'Letra', 'dataType' => 'Center', 'width' => 10),
			   array('content' => 'Número', 'dataType' => 'Entero', 'width' => 10),
			   array('content' => 'Detalle', 'width' => 30),
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

		$cells[0][0]->colspan = 7;
		$cells[0][0]->content = 'SALDO A FECHA ' . Funciones::sumarTiempo($cc->fechaDesde, -1);
		$cells[0][0]->class .= ' aCenter bold';
		$saldo = $totalEmpresa1 + $totalEmpresa2;
		$cells[0][7]->content = Funciones::formatearMoneda($saldo);

		$j++;
	}

	for ($i = 0; $i < $tabla->cantRows; $i++) {
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

		$cells[$j][0]->content = $doc->fecha;
		$cells[$j][1]->content = $doc->tipoDocumento;
		$cells[$j][2]->content = $doc->letra;
		$cells[$j][3]->content = $doc->numero;
		$cells[$j][4]->content = $doc->detalle;
		$cells[$j][5]->content = $debe;
		$cells[$j][6]->content = $haber;
		$cells[$j][7]->content = $saldo;

		if ($doc->empresa == 1){
			$rows[$i]->class .= ' bold';
			$totalEmpresa1 += $doc->importeTotal;
		}else{
			$totalEmpresa2 += $doc->importeTotal;
		}
		$j++;
	}

	$tablaTotales = new HtmlTable(array('cantRows' => 2, 'cantCols' => 1, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '100%'));
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