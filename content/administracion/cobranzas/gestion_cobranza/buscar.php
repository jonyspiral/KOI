<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/gestion_cobranza/buscar/')) { ?>
<?php

$idVendedor = Usuario::logueado()->esVendedor() ? Usuario::logueado()->personal->id : Funciones::get('idVendedor');
$idCliente = Funciones::get('idCliente');
$saldoDesde = Funciones::get('saldoDesde');
$saldoHasta = Funciones::get('saldoHasta');
$saldoFechaHasta = Funciones::get('saldoFechaHasta');
$orden = Funciones::get('orden');
$modoVendedor = (Usuario::logueado()->esVendedor() || Funciones::get('vendedor'));
$empresa = Funciones::get('empresa');

try {
	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= $idVendedor ? 'cod_vendedor = ' . Datos::objectToDB($idVendedor) . ' AND ' : '';
	$where .= $idCliente ? 'cod_cli = ' . Datos::objectToDB($idCliente) . ' AND ' : '';

	$whereAux = '';
	for($i = 1; $i < 8; $i++) {
		if(Funciones::get('situacion' . $i) == 'S') {
			$whereAux .= 'cod_calificacion = ' . Datos::objectToDB('0' . $i) . ' OR ';
		}
	}
	if($whereAux){
		$where .= '(' . trim($whereAux, ' OR ') . ') AND ';
	}
	if (!$saldoFechaHasta) {
		$where .= $saldoDesde ? 'saldo >= ' . Datos::objectToDB($saldoDesde) . ' AND ' : '';
		$where .= $saldoHasta ? 'saldo <= ' . Datos::objectToDB($saldoHasta) . ' AND ' : '';
	}
	$where .= ($modoVendedor ? 'cod_calificacion != ' . Datos::objectToDB('05') . ' AND ' : '');
	$where = trim($where, ' AND ');
	$order = '';
	switch ($orden) {
		case 1: $order .= 'cod_calificacion ASC'; break;
		case 2: $order .= 'cod_calificacion DESC'; break;
		case 3: $order .= 'saldo ASC'; break;
		case 4: $order .= 'saldo DESC'; break;
		case 5: $order .= '(saldo + total_cheques) ASC'; break;
		case 6: $order .= '(saldo + total_cheques) DESC'; break;
		case 7: $order .= 'dias_promedio_pago ASC'; break;
		case 8: $order .= 'dias_promedio_pago DESC'; break;
	}
	$order = 'ORDER BY ' . trim($order . ', razon_social ASC', ', ');
	$clientes = Factory::getInstance()->getArrayFromView('clientes_v', $where . $order);

	if (!count($clientes)) {
		throw new FactoryExceptionCustomException('No existen registros con el filtro especificado');
	}

	$saldos = array();
	if ($saldoFechaHasta) {
		$saldosAFecha = Factory::getInstance()->getArrayFromStoredProcedure('saldo_clientes_a_fecha', Datos::objectToDB($saldoFechaHasta));
		if($empresa != NULL){
			$array_borrar = array();
			$indice = 0;
			foreach ($saldosAFecha as $saldo){
				if($saldo['empresa'] != $empresa){
					array_push($array_borrar,$indice);
				}
				$indice++;
			}
			foreach($array_borrar as $borrar){
				unset($saldosAFecha[$borrar]);
			}
			$saldosAFecha = array_values($saldosAFecha);
		}
		foreach ($saldosAFecha as $saldo) {
			$saldos[$saldo['cod_cli']] = Funciones::toFloat($saldo['saldo']);
		}
	}

	$restaModoVendedor = ($modoVendedor ? 3 : 0);

	$tabla = new HtmlTable(array('cantRows' => count($clientes), 'cantCols' => (10 - $restaModoVendedor), 'id' => 'tablaDatos', 'class' => 'registrosAlternados', 'cellSpacing' => 0, 'width' => '99%',
								'tdBaseClass' => 'pRight10 pLeft10 bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bLeftDarkGray bRightDarkGray'));
	$tabla->getRowCellArray($rows, $cells);
	$headerConfig = array(
		array('content' => 'Cliente', 'width' => 16),
		array('content' => 'Vendedor', 'width' => 16),
		array('content' => 'Calif.', 'dataType' => 'Center', 'width' => 6, 'title' => 'Calificación'),
		array('content' => 'Saldo', 'dataType' => 'Moneda', 'width' => 8),
		array('content' => 'Saldo Cheq.', 'dataType' => 'Moneda', 'width' => 9),
		array('content' => 'Ingr. mes', 'dataType' => 'Moneda', 'width' => 7, 'title' => 'Pagos ingresados mes actual')
	);
	if (!$modoVendedor) {
		$headerConfig[] = array('content' => 'D.P.P.', 'dataType' => 'Center', 'width' => 5, 'title' => 'Días promedio de pago');
		$headerConfig[] = array('content' => 'S/aplicar debe', 'dataType' => 'Center', 'width' => 10);
		$headerConfig[] = array('content' => 'S/aplicar haber', 'dataType' => 'Center', 'width' => 10);
	}
	$headerConfig[] = array('content' => 'Observaciones', 'width' => ($modoVendedor ? 37 : 13));
	$tabla->createHeaderFromArray($headerConfig);

	$claseCalificacion = array(
		'01' => array('fondo' => '#9FE4E7', 'letra' => '#19878B'),
		'02' => array('fondo' => '#B4E79F', 'letra' => '#378816'),
		'03' => array('fondo' => '#FFF2AB', 'letra' => '#927E10'),
		'04' => array('fondo' => '#ECB68B', 'letra' => '#A34F0C'),
		'05' => array('fondo' => '#E98989', 'letra' => '#B32F2F'),
		'07' => array('fondo' => '#9B9B9B', 'letra' => '#000000')
	);

	$saldoFinal = 0;
	$saldoFinalCheq = 0;
	for ($i = 0; $i < $tabla->cantRows; $i++) {
		$cli = $clientes[$i];
		$cli['saldo'] = Funciones::toFloat($cli['saldo']);
		//Cuando ponen saldoFechaHasta, el filtro de importes de saldo lo tengo que hacer a mano:
		if (($saldoFechaHasta) && (($saldoDesde && $cli['saldo'] < $saldoDesde) || ($saldoHasta && $cli['saldo'] > $saldoHasta))) {
			continue;
		}

		$saldoCheques = ($cli['total_cheques'] + ($cli['saldo'] > 0 ? $cli['saldo'] : 0));

		$saldoFinal += $cli['saldo'];
		$saldoFinalCheq += $saldoCheques;

		$rows[$i]->id = $cli['cod_cli'];
		$rows[$i]->class = 's13';
		$cells[$i][0]->style->height = '38px';
		$cells[$i][0]->content = '[' . $cli['cod_cli'] . '] ' . $cli['razon_social'];
		$cells[$i][0]->class .= ($modoVendedor ? ' cliente' : ' seguimiento') . ' cPointer';
		$cells[$i][0]->title = 'Ir al seguimiento del cliente';
		$cells[$i][1]->content = '[' . $cli['cod_vendedor'] . '] ' . $cli['nombre_vendedor'];
		$cells[$i][2]->content = $cli['cod_calificacion'];
		$cells[$i][2]->class .= ' calificacion cPointer c_' . $cli['cod_calificacion'];
		if ($modoVendedor) {
			$cells[$i][2]->style->background_color = $claseCalificacion[$cli['cod_calificacion']]['fondo']; //Fix para el PDF
			$cells[$i][2]->style->color = $claseCalificacion[$cli['cod_calificacion']]['letra']; //Fix para el PDF
		}
		$cells[$i][3]->content = $saldoFechaHasta ? $saldos[$cli['cod_cli']] : $cli['saldo'];
		$cells[$i][3]->class .= ' saldo cPointer';
		$cells[$i][3]->title = 'Ir a la cuenta corriente del cliente';
		$cells[$i][4]->content = ($saldoFechaHasta ? '' : $saldoCheques);
		$cells[$i][4]->title = 'Ir a la cuenta corriente del cliente';
		$cells[$i][5]->content = $cli['pagos_ingresados_mes'];
		if (!$modoVendedor) {
			$cells[$i][6]->content = $cli['dias_promedio_pago'];
			$cells[$i][7]->content = is_null($cli['fecha_debe']) ? '' : (Funciones::formatearFecha($cli['fecha_debe']) . '<br>' . Funciones::formatearMoneda($cli['importe_pendiente_debe']));
			$cells[$i][7]->class .= ' aplicador cPointer';
			$cells[$i][7]->title = 'Ir al aplicador';
			$cells[$i][8]->content = is_null($cli['fecha_haber']) ? '' : (Funciones::formatearFecha($cli['fecha_haber']) . '<br>' . Funciones::formatearMoneda(-1 * $cli['importe_pendiente_haber']));
			$cells[$i][8]->class .= ' aplicador cPointer';
			$cells[$i][8]->title = 'Ir al aplicador';
		}
		$cells[$i][9 - $restaModoVendedor]->content = '<span class="obs_cli">' . $cli['observaciones_gestion_cobranza'] . '</span>';
		$cells[$i][9 - $restaModoVendedor]->content .= '<br><span class="obs_ven" style="color: #B94A48;">' . $cli['observaciones_vendedor'] . '</span>';
		$cells[$i][9 - $restaModoVendedor]->class .= ' observaciones cPointer';
	}

	$tabla->getFootArray($foots);
	$foots[2]->class = 'bold p10 bLightOrange bTopWhite cornerBL5';
	$foots[2]->content = 'Total:';
	$foots[3]->class = 'bold p10 bLightOrange aRight bTopWhite bLeftWhite';
	$foots[3]->content = Funciones::formatearMoneda($saldoFinal);
	$foots[4]->class = 'bold p10 bLightOrange aRight bTopWhite bLeftWhite cornerBR5';
	$foots[4]->content = Funciones::formatearMoneda($saldoFinalCheq);

	$html = $tabla->create(true);
	echo $html;
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>