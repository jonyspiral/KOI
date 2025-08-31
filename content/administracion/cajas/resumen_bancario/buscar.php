<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cajas/resumen_bancario/buscar/')) { ?>
<?php

$idCaja = Funciones::get('caja');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$empresa = Funciones::get('empresa');

function getSaldos($idCaja, $empresa, $desde, $hasta) {
	$whereEmpresa = (($empresa == '1' || $empresa == '2') ? ' AND empresa = ' . Datos::objectToDB($empresa) : '');

	$fieldsInicial = 'SUM((CASE tipo WHEN ' . Datos::objectToDB('E') . ' THEN -1 ELSE 1 END) * importe) saldo, (SELECT importe_efectivo FROM caja WHERE cod_caja = ' . Datos::objectToDB($idCaja) . ') importe';
	$whereInicial = 'cod_caja = ' . Datos::objectToDB($idCaja) . $whereEmpresa;

	$fieldsDesde = 'SUM((CASE tipo WHEN ' . Datos::objectToDB('E') . ' THEN -1 ELSE 1 END) * importe) saldo_desde';
	$whereDesde = 'cod_caja = ' . Datos::objectToDB($idCaja) . ' AND fecha < dbo.toDate(' . Datos::objectToDB($desde) . ')' . $whereEmpresa;

	$fieldsHasta = 'SUM((CASE tipo WHEN ' . Datos::objectToDB('E') . ' THEN -1 ELSE 1 END) * importe) saldo_hasta';
	$whereHasta = 'cod_caja = ' . Datos::objectToDB($idCaja) . ($hasta ? ' AND fecha <= dbo.toDate(' . Datos::objectToDB($hasta) . ')' : '') . $whereEmpresa;

	$saldoInicial = Factory::getInstance()->getArrayFromView('resumen_bancario_v', $whereInicial, 0, $fieldsInicial);
	$saldoInicial = array('importe_inicial' => (Funciones::toFloat($saldoInicial[0]['importe']) - Funciones::toFloat($saldoInicial[0]['saldo'])));
	$saldosDesde = Factory::getInstance()->getArrayFromView('resumen_bancario_v', $whereDesde, 0, $fieldsDesde);
	$saldosDesde = $saldosDesde[0];
	$saldosHasta = Factory::getInstance()->getArrayFromView('resumen_bancario_v', $whereHasta, 0, $fieldsHasta);
	$saldosHasta = $saldosHasta[0];

	return array_merge($saldoInicial, $saldosDesde, $saldosHasta);
}

try {
	$totalDebe = 0;
	$totalHaber = 0;
	if(empty($idCaja)) {
		throw new FactoryExceptionCustomException('Debe especificar una caja.');
	}

	//Armo el where
	$where = Funciones::strFechas($desde, $hasta, 'fecha', true) . ' AND ';
	$where .= 'cod_caja = ' . Datos::objectToDB($idCaja);
	$where .= ($empresa == '1' || $empresa == '2') ? ' AND empresa = ' . Datos::objectToDB($empresa) : '';
	$where = trim($where, ' AND ');
	$orderBy = ' ORDER BY fecha ASC, cod_importe_operacion ASC';

	$listaMovimientos = Factory::getInstance()->getArrayFromView('resumen_bancario_v', $where . $orderBy);
	if (count($listaMovimientos) == 0) {
		throw new FactoryExceptionCustomException('No existen movimientos con el filtro especificado');
	}

	$tabla = new HtmlTable(array('cantRows' => count($listaMovimientos) + 2, 'cantCols' => 9, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'Fecha', 'width' => 9),
			 array('content' => 'E', 'dataType' => 'Center', 'width' => 2),
			 array('content' => 'Doc.', 'dataType' => 'Center', 'width' => 5),
			 array('content' => 'Nº', 'dataType' => 'Entero', 'width' => 5),
			 array('content' => 'Detalle', 'width' => 25),
			 array('content' => 'Obs.', 'width' => 18),
			 array('content' => 'Debe', 'dataType' => 'Moneda', 'width' => 12),
			 array('content' => 'Haber', 'dataType' => 'Moneda', 'width' => 12),
			 array('content' => 'Saldo', 'dataType' => 'Moneda', 'width' => 12)
		)
	);

	$initVal = 0;
	$initVal++;
	$arraySaldos = getSaldos($idCaja, $empresa, $desde, $hasta);

	/* *** SALDO INICIAL *** */
	$saldoTotalDesde = $arraySaldos['importe_inicial'] + Funciones::toFloat($arraySaldos['saldo_desde']);

	/* *** SALDO FINAL *** */
	$saldoTotalHasta = $arraySaldos['importe_inicial'] + Funciones::toFloat($arraySaldos['saldo_hasta']);

	$config = array(
		0 => array(
			'titulo'		=> 'Saldo inicial',
			'saldoTotal'	=> $saldoTotalDesde
		),
		($tabla->cantRows - 1) => array(
			'titulo'		=> 'Saldo final',
			'saldoTotal'	=> $saldoTotalHasta
		)
	);

	for ($i = 0; $i < count($listaMovimientos); $i++) {
		$k = $i + $initVal;
		$item = $listaMovimientos[$i];

		for ($j = 0; $j < $tabla->cantCols; $j++) {
			if ($j == 0) $cells[$k][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
			else $cells[$k][$j]->class .= ' bBottomDarkGray';
			if ($j == ($tabla->cantCols - 1)) $cells[$k][$j]->class .= ' bRightDarkGray bold';
		}

		$rows[$k]->class = $item['tipo'] == 'I' ? 'indicador-verde' : 'indicador-rojo';

		$saldoTotalDesde = $saldoTotalDesde + (($item['tipo'] == 'I' ? 1 : -1) * $item['importe']);

		$cells[$k][0]->content = Funciones::formatearFecha($item['fecha'], 'd/m/Y');
		$cells[$k][0]->class .= ' aCenter';
		$cells[$k][1]->content = $item['empresa'];
		$cells[$k][2]->content = $item['tipo_documento'];
		$cells[$k][3]->content = $item['numero'];
		$cells[$k][4]->content = (empty($item['detalle']) ? '-' : $item['detalle']);
		$cells[$k][4]->class .= ' s11';
		$cells[$k][5]->content = (empty($item['observaciones']) ? '-' : $item['observaciones']);
		$cells[$k][5]->class .= ' s11';
		$cells[$k][6]->content = Funciones::formatearMoneda(($item['tipo'] == 'E' ? $item['importe'] : 0)); //Le pongo formatear moneda porque no le puse "TYPE = MONEDA" en el header (por los saldos iniciales y final)
		$cells[$k][7]->content = Funciones::formatearMoneda(($item['tipo'] == 'I' ? $item['importe'] : 0)); //Le pongo formatear moneda porque no le puse "TYPE = MONEDA" en el header (por los saldos iniciales y final)
		$cells[$k][8]->content = $saldoTotalDesde;

		$totalDebe += ($item['tipo'] == 'E' ? $item['importe'] : 0);
		$totalHaber += ($item['tipo'] == 'I' ? $item['importe'] : 0);
	}

	foreach ($config as $k => $c) {
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			if ($j == 0) $cells[$k][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
			else $cells[$k][$j]->class .= ' bBottomDarkGray';
			if ($j == ($tabla->cantCols - 1)) $cells[$k][$j]->class .= ' bRightDarkGray bold';
		}

		$rows[$k]->class = 'bDarkOrange white';

		$cells[$k][0]->content = $c['titulo'];
		$cells[$k][0]->class .= ' aCenter bold';
		$cells[$k][8]->content = $c['saldoTotal'];

		if($k == 0){
			$cells[$k][0]->colspan = 8;
		}else{
			$cells[$k][0]->colspan = 6;
			$cells[$k][6]->content = $totalDebe;
			$cells[$k][7]->content = $totalHaber;
		}
	}

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