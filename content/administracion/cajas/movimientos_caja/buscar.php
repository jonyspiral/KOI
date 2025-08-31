<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cajas/movimientos_caja/buscar/')) { ?>
<?php

$idCaja = Funciones::get('caja');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$empresa = Funciones::get('empresa');
$soloEfectivo = Funciones::get('soloEfectivo') == 'S';

function getSaldos($idCaja, $empresa, $desde) {
	$whereEmpresa = (($empresa == '1' || $empresa == '2') ? ' AND empresa = ' . Datos::objectToDB($empresa) : '');

	$fieldsInicial = 'SUM((CASE tipo WHEN' . Datos::objectToDB('E') . 'THEN -1 ELSE 1 END) * efectivo) saldo, (SELECT importe_efectivo FROM caja WHERE cod_caja = ' . Datos::objectToDB($idCaja) . ') importe_efectivo';
	$whereInicial = 'cod_caja = ' . Datos::objectToDB($idCaja) . $whereEmpresa . ' AND anulado = ' . Datos::objectToDB('N');

	$fieldsDesde = 'SUM((CASE tipo WHEN' . Datos::objectToDB('E') . 'THEN -1 ELSE 1 END) * efectivo) saldo_desde';
	$whereDesde = 'cod_caja = ' . Datos::objectToDB($idCaja) . ' AND fecha < dbo.relativeDate(dbo.toDate(' . Datos::objectToDB($desde) . '), ' . Datos::objectToDB('today') . ', 0)' . $whereEmpresa . ' AND anulado = ' . Datos::objectToDB('N');

	$saldoInicial = Factory::getInstance()->getArrayFromView('movimientos_caja_v_noanul', $whereInicial, 0, $fieldsInicial);
	$saldoInicial1 = Funciones::toFloat($saldoInicial[0]['importe_efectivo']) - Funciones::toFloat($saldoInicial[0]['saldo']);
	$saldosDesde = Factory::getInstance()->getArrayFromView('movimientos_caja_v_noanul', $whereDesde, 0, $fieldsDesde);
	$saldosDesde1 = $saldosDesde[0]['saldo_desde'];

	/*$saldoInicial = Factory::getInstance()->getArrayFromView('movimientos_caja_v_anul', $whereInicial, 0, $fieldsInicial);
	$saldoInicial2 = Funciones::toFloat($saldoInicial[0]['importe_efectivo']) - Funciones::toFloat($saldoInicial[0]['saldo']);
	$saldosDesde = Factory::getInstance()->getArrayFromView('movimientos_caja_v_anul', $whereDesde, 0, $fieldsDesde);
	$saldosDesde2 = $saldosDesde[0]['saldo_desde'];*/

	return array(
		'importe_inicial' => $saldoInicial1 + $saldoInicial2,
		'saldo_desde' => $saldosDesde1 + $saldosDesde2
	);
}

function merge($listaMovimientos, $listaCheques) {
	if (count($listaMovimientos) == 0) {
		return $listaCheques;
	}
	if (count($listaCheques) == 0) {
		return $listaMovimientos;
	}
	$listaFinal = array();
	$i = $j = 0;
	while ($i < count($listaMovimientos) && $j < count($listaCheques)) {
		if (Funciones::esFechaMayor($listaMovimientos[$i]['fecha'], $listaCheques[$j]['fecha'])) {
			$listaFinal[] = $listaCheques[$j];
			$j++;
		} else {
			$listaFinal[] = $listaMovimientos[$i];
			$i++;
		}
	}
	while ($i < count($listaMovimientos)) {
		$listaFinal[] = $listaMovimientos[$i];
		$i++;
	}
	while ($j < count($listaCheques)) {
		$listaFinal[] = $listaCheques[$j];
		$j++;
	}
	return $listaFinal;
}

try {
	if(empty($idCaja)) {
		throw new FactoryExceptionCustomException('Debe especificar una caja.');
	}

	//Armo el where
	$where = Funciones::strFechas($desde, $hasta, 'fecha', true) . ' AND ';
	$where .= 'cod_caja = ' . Datos::objectToDB($idCaja) . ' AND ';
	$where .= ($empresa == '1' || $empresa == '2') ? 'empresa = ' . Datos::objectToDB($empresa) . ' AND ' : '';
	$where .= ($soloEfectivo) ? 'efectivo > ' . Datos::objectToDB(0) . ' AND ' : '';
	$where = trim($where, ' AND ');
	$orderBy = ' ORDER BY fecha ASC, cod_importe_operacion ASC';

	//$listaMovimientos = Factory::getInstance()->getArrayFromView('movimientos_caja_v', $where . $orderBy);
	$listaMovimientos = Factory::getInstance()->getArrayFromStoredProcedure('movimientos_caja_sp', '@where = ' . Datos::objectToDB($where));
	$listaCheques = Factory::getInstance()->getArrayFromView('movimientos_caja_v_chq', $where . $orderBy);

	if ((count($listaMovimientos) + count($listaCheques)) == 0) {
		throw new FactoryExceptionCustomException('No existen movimientos con el filtro especificado');
	}

	$listaMovimientos = merge($listaMovimientos, $listaCheques);

	$tabla = new HtmlTable(array('cantRows' => count($listaMovimientos) + 2, 'cantCols' => 13, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'Tipo', 'width' => 4),
			 array('content' => 'Fecha', 'width' => 8),
			 array('content' => 'E', 'dataType' => 'Center', 'width' => 2),
			 array('content' => 'Doc.', 'dataType' => 'Center', 'width' => 5),
			 array('content' => 'Nº', 'dataType' => 'Entero', 'width' => 5),
			 array('content' => 'De', 'width' => 13),
			 array('content' => 'Para', 'width' => 13),
			 array('content' => 'Efectivo', 'dataType' => 'Right', 'width' => 8),
			 array('content' => 'Cheques', 'dataType' => 'Moneda', 'width' => 8),
			 array('content' => 'Transf.', 'dataType' => 'Right', 'width' => 8),
			 array('content' => 'Reten.', 'dataType' => 'Moneda', 'width' => 8),
			 array('content' => 'Total', 'dataType' => 'Moneda', 'width' => 9),
			 array('content' => 'Saldo (Efvo.)', 'dataType' => 'Moneda', 'width' => 9)
		)
	);

	$arraySaldos = getSaldos($idCaja, $empresa, $desde);

	/* *** SALDO INICIAL *** */
	$saldoTotalDesde = $arraySaldos['importe_inicial'] + Funciones::toFloat($arraySaldos['saldo_desde']);
	$totalAcumuladoEfectivo = $saldoTotalDesde;

	for ($i = 0; $i < count($listaMovimientos); $i++) {
		$k = $i + 1;
		$item = $listaMovimientos[$i];

		for ($j = 0; $j < $tabla->cantCols; $j++) {
			if ($j == 0) $cells[$k][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
			else $cells[$k][$j]->class .= ' bBottomDarkGray';
			if ($j == ($tabla->cantCols - 1)) $cells[$k][$j]->class .= ' bRightDarkGray bold';
		}

		if($item['tipo'] == 'I'){
			$rows[$k]->class = 'indicador-verde';
		} else {
			$rows[$k]->class = 'indicador-rojo';
			$item['efectivo'] = -$item['efectivo'];
			$item['cheques'] = -$item['cheques'];
			$item['transferencias'] = -$item['transferencias'];
			$item['retenciones'] = -$item['retenciones'];
			$item['total'] = -$item['total'];
		}

		if($item['anulado'] == 'S'){
			$rows[$k]->class .= ' selected';
		}

		$totalAcumuladoEfectivo += $item['efectivo'];

		$cells[$k][0]->content = $item['tipo'] == 'E' ? 'EGR' : 'ING';
		$cells[$k][0]->class .= ' aCenter';
		$cells[$k][1]->content = Funciones::formatearFecha($item['fecha'], 'd/m/Y');
		$cells[$k][1]->class .= ' aCenter';
		$cells[$k][2]->content = $item['empresa'];
		$cells[$k][3]->content = $item['tipo_documento'];
		$cells[$k][4]->content = $item['numero'];
		$cells[$k][5]->content = Funciones::acortar($item['de'], 30);
		$cells[$k][5]->class .= ' s11';
		$cells[$k][6]->content = Funciones::acortar($item['para'], 30);
		$cells[$k][6]->class .= ' s11';
		$cells[$k][7]->content = Funciones::formatearMoneda($item['efectivo']); //Le pongo formatear moneda porque no le puse "TYPE = MONEDA" en el header (por los saldos iniciales y final)
		$cells[$k][8]->content = $item['cheques'];
		$cells[$k][9]->content = Funciones::formatearMoneda($item['transferencias']); //Le pongo formatear moneda porque no le puse "TYPE = MONEDA" en el header (por los saldos iniciales y final)
		$cells[$k][10]->content = $item['retenciones'];
		$cells[$k][11]->content = $item['total'];
		$cells[$k][12]->content = $totalAcumuladoEfectivo;
	}

	$config = array(
		0 => array(
			'titulo'		=> 'Saldo inicial efectivo',
			'saldoTotal'	=> $saldoTotalDesde
		),
		($tabla->cantRows - 1) => array(
			'titulo'		=> 'Saldo final efectivo',
			'saldoTotal'	=> $totalAcumuladoEfectivo
		)
	);

	foreach ($config as $k => $c) {
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			if ($j == 0) $cells[$k][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
			else $cells[$k][$j]->class .= ' bBottomDarkGray';
			if ($j == ($tabla->cantCols - 1)) $cells[$k][$j]->class .= ' bRightDarkGray bold';
		}

		$rows[$k]->class = 'bDarkOrange white';

		$cells[$k][0]->content = $c['titulo'];
		$cells[$k][0]->colspan = 12;
		$cells[$k][0]->class .= ' aCenter bold';
		$cells[$k][12]->content = $c['saldoTotal'];
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