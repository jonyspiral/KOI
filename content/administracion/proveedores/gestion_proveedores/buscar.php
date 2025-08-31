<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/gestion_proveedores/buscar/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');
$saldoDesde = Funciones::get('saldoDesde');
$saldoHasta = Funciones::get('saldoHasta');
$saldoFechaHasta = Funciones::get('saldoFechaHasta');
$mostrarSaldoCero = Funciones::get('mostrarSaldoCero') == 'S';
$empresa = Funciones::get('empresa');
$orden = Funciones::get('orden');

try {
	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= $idProveedor ? 'cod_prov = ' . Datos::objectToDB($idProveedor) . ' AND ' : '';
	if (!$saldoFechaHasta) {
		$where .= $saldoDesde ? 'saldo >= ' . Datos::objectToDB($saldoDesde) . ' AND ' : '';
		$where .= $saldoHasta ? 'saldo <= ' . Datos::objectToDB($saldoHasta) . ' AND ' : '';
		if (!$mostrarSaldoCero) {
			$where .= '(saldo > ' . Datos::objectToDB(0.009) . ' OR saldo < ' . Datos::objectToDB(-0.009) . ') AND ';
		}
	}
	$where = trim($where, ' AND ');
	$order = '';
	switch ($orden) {
		case 1: $order .= 'saldo ASC'; break;
		case 2: $order .= 'saldo DESC'; break;
		case 3: $order .= 'imputacion_especifica ASC'; break;
		case 4: $order .= 'imputacion_especifica DESC'; break;
	}
	$order = 'ORDER BY ' . trim($order . ', razon_social ASC', ', ');
	$proveedores = Factory::getInstance()->getArrayFromView('gestion_proveedores' . ($empresa ? '_' . $empresa : ''), $where . $order);

	if (!count($proveedores)) {
		throw new FactoryExceptionCustomException('No existen registros con el filtro especificado');
	}

	$saldos = array();
	if ($saldoFechaHasta) {
		$saldosAFecha = Factory::getInstance()->getArrayFromStoredProcedure('saldo_proveedores_a_fecha', Datos::objectToDB($saldoFechaHasta));
		foreach ($saldosAFecha as $saldo) {
			$saldos[$saldo['cod_prov']] = Funciones::toFloat($saldo['saldo']);
		}
	}
	$tabla = new HtmlTable(array('cantRows' => count($proveedores), 'cantCols' => 6, 'id' => 'tablaDatos', 'class' => 'registrosAlternados', 'cellSpacing' => 0, 'width' => '99%'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'Proveedor', 'width' => 20),
			 array('content' => 'CUIT', 'dataType' => 'Center', 'width' => 12),
			 array('content' => 'Saldo', 'dataType' => 'Moneda', 'width' => 11),
			 array('content' => 'Saldo Cheq.', 'dataType' => 'Moneda', 'width' => 11),
			 array('content' => 'Imputación', 'width' => 20),
			 array('content' => 'Observaciones', 'width' => 25)
		)
	);

	$saldoFinal = 0;
	for ($i = 0; $i < $tabla->cantRows; $i++) {
		$pro = $proveedores[$i];
		$pro['saldo'] = Funciones::toFloat($pro['saldo']);
		//Cuando ponen saldoFechaHasta, el filtro de importes de saldo lo tengo que hacer a mano:
		if (($saldoFechaHasta) && (($saldoDesde && $pro['saldo'] < $saldoDesde) || ($saldoHasta && $pro['saldo'] > $saldoHasta) || (!$mostrarSaldoCero && ($pro['saldo'] < 0.009 || $pro['saldo'] > -0.009)))) {
			continue;
		}
		$saldoFinal += Funciones::toFloat($pro['saldo']);
		for($j = 0; $j < $tabla->cantCols; $j++) {
			$cells[$i][$j]->class = 'pRight10 pLeft10 bLeftDarkGray' . ($i == ($tabla->cantRows - 1) ? ' bBottomDarkGray' : '');
			if ($j == ($tabla->cantCols - 1)) $cells[$i][$j]->class .= ' bRightDarkGray';
		}
		$rows[$i]->id = $pro['cod_prov'];
		$cells[$i][0]->content = '[' . $pro['cod_prov'] . '] ' . $pro['razon_social'];
		$cells[$i][0]->class .= ' nombre cPointer';
		$cells[$i][1]->content = Funciones::ponerGuionesAlCuit($pro['cuit']);
		$cells[$i][2]->content = $saldoFechaHasta ? $saldos[$pro['cod_prov']] : $pro['saldo'];
		$cells[$i][2]->class .= ' saldo cPointer';
		$cells[$i][2]->title = 'Ir al aplicador';
		$cells[$i][3]->content = $saldoFechaHasta ? '' : ($pro['total_cheques'] + ($pro['saldo'] > 0 ? $pro['saldo'] : 0));
		$cells[$i][4]->content = $pro['imputacion_especifica'] . ' - ' . $pro['denominacion'];
		$cells[$i][5]->content = $pro['observaciones_gestion'];
		$cells[$i][5]->class .= ' observaciones cPointer';
	}

	$tabla->getFootArray($foots);
	$foots[1]->class = 'bold p10 bLightOrange bTopWhite cornerBL5';
	$foots[1]->content = 'Total:';
	$foots[2]->class = 'bold p10 bLightOrange aRight bTopWhite bLeftWhite cornerBR5';
	$foots[2]->content = Funciones::formatearMoneda($saldoFinal);

	$html = $tabla->create(true);
	echo $html;
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>