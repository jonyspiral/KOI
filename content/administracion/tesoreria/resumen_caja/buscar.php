<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/subdiario_ingresos/buscar/')) { ?>
<?php

function f($num) {
	return Funciones::formatearDecimales($num, 2);
}

function strFechas($desde, $hasta){
	$strFechas = '';
	if (isset($desde))
		$strFechas = ' AND fecha >= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($desde)) . ')';
	if (isset($hasta))
		if ($strFechas != '')
		$strFechas .= ' AND fecha <= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . ')';
	else
		$strFechas = ' AND (fecha <= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . ')) ';
	else
		if ($strFechas != '')
		$strFechas .= ') ';
	return $strFechas;
}

function comprobarFechas(&$desde, &$hasta) {
	if (!isset($desde) && !isset($hasta))
		throw new FactoryException('Debe ingresar una fecha "desde" o una fecha "hasta"');
	
	if (!isset($desde))
		$desde = Funciones::sumarTiempo($hasta, -45, 'days');
	if (!isset($hasta))
		$hasta = Funciones::sumarTiempo($desde, 45, 'days');

	if (Funciones::esFechaMenor($hasta, $desde))
		throw new FactoryException('La fecha "desde" no puede ser posterior a la fecha "hasta"');

	if (Funciones::diferenciaFechas($hasta, $desde) > 45)
		throw new FactoryException('El rango de fechas no puede superar los 45 días');
}

function armoHead(&$tabla) {
	$ths = array();
	$tabla->getHeadArray($ths);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		if ($i == 0) $ths[$i]->class = 'cornerL5 w34p';
		elseif ($i == 6) $ths[$i]->class = 'cornerR5 w11p bLeftWhite';
		else $ths[$i]->class = 'w11p bLeftWhite';
	}
	$tabla->headerClass('tableHeader');
	$ths[0]->content = 'Concepto';
	$ths[1]->content = 'Efectivo';
	$ths[1]->dataType = 'DosDecimales';
	$ths[2]->content = 'Cheques';
	$ths[2]->dataType = 'DosDecimales';
	$ths[3]->content = 'Ret. Gcias.';
	$ths[3]->dataType = 'DosDecimales';
	$ths[4]->content = 'Ret. IVA';
	$ths[4]->dataType = 'DosDecimales';
	$ths[5]->content = 'Ret. IIBB';
	$ths[5]->dataType = 'DosDecimales';
	$ths[6]->content = 'Total';
	$ths[6]->dataType = 'DosDecimales';
	return $tabla;
}

function armarTablaSaldoAnterior(&$html, $cajaAnterior, $caja, $saldoFinal = false) {
	$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 7, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	
	$concepto = 'Saldo anterior (' . $cajaAnterior->fecha . ')';
	if ($saldoFinal)
		$concepto = 'Saldo final';
	else
		$tabla->caption = 'PARTE DIARIO DE CAJA: ' . $caja->fecha;

	armoHead($tabla);

	$tabla->getRowCellArray($rows, $cells);
	for($i = 0; $i < $tabla->cantRows; $i++) {
		for($j = 0; $j < $tabla->cantCols; $j++) {
			$cells[$i][$j]->class = 'pRight5 pLeft5';
			if ($j == 0)
				$cells[$i][$j]->class .= ' bAllDarkGray';
			else
				$cells[$i][$j]->class .= ' bTopDarkGray bBottomDarkGray bRightDarkGray';
		}
	}
	$cells[0][0]->content = $concepto;
	$cells[0][1]->content = f($cajaAnterior->importe1Efectivo);
	$cells[0][2]->content = f($cajaAnterior->importe2Cheque);
	$cells[0][3]->content = f($cajaAnterior->importe4RetencionGanancias);
	$cells[0][4]->content = f($cajaAnterior->importe5RetencionIva);
	$cells[0][5]->content = f($cajaAnterior->importe6RetencionIIBB);
	$cells[0][6]->content = f($cajaAnterior->importeTotal);

	$rows[0]->class = 'bold';

	$htmlTabla = $tabla->create(true);
	$html .= $htmlTabla;
	return $htmlTabla;
}

function armarTablaIngresos(&$html, $caja) {
	$tabla = new HtmlTable(array('cantRows' => 3, 'cantCols' => 7, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	armoHead($tabla);

	$tabla->getRowCellArray($rows, $cells);
	for($i = 0; $i < $tabla->cantRows; $i++) {
		for($j = 0; $j < $tabla->cantCols; $j++) {
			$cells[$i][$j]->class = 'pRight5 pLeft5';
			if ($j == 0)
				$cells[$i][$j]->class .= ' bLeftDarkGray bRightDarkGray';
			else
				$cells[$i][$j]->class .= ' bRightDarkGray';
			if ($i == 2)
				$cells[$i][$j]->class .= ' bTopDarkGray bBottomDarkGray';
		}
	}

	$cells[0][0]->content = 'Cobranza deudores';
	$cells[0][1]->content = f($caja->cobranzaDeudores1Efectivo);
	$cells[0][2]->content = f($caja->cobranzaDeudores2Cheque);
	$cells[0][3]->content = f($caja->cobranzaDeudores4RetencionGanancias);
	$cells[0][4]->content = f($caja->cobranzaDeudores5RetencionIva);
	$cells[0][5]->content = f($caja->cobranzaDeudores6RetencionIIBB);
	$cells[0][6]->content = f($caja->cobranzaDeudoresTotal);

	$cells[1][0]->content = 'Otros ingresos';
	$cells[1][1]->content = f($caja->otrosIngresos1Efectivo);
	$cells[1][2]->content = f($caja->otrosIngresos2Cheque);
	$cells[1][3]->content = f($caja->otrosIngresos4RetencionGanancias);
	$cells[1][4]->content = f($caja->otrosIngresos5RetencionIva);
	$cells[1][5]->content = f($caja->otrosIngresos6RetencionIIBB);
	$cells[1][6]->content = f($caja->otrosIngresosTotal);

	$cells[2][0]->content = 'Total ingresos';
	$cells[2][1]->content = f($caja->cobranzaDeudores1Efectivo + $caja->otrosIngresos1Efectivo);
	$cells[2][2]->content = f($caja->cobranzaDeudores2Cheque + $caja->otrosIngresos2Cheque);
	$cells[2][3]->content = f($caja->cobranzaDeudores4RetencionGanancias + $caja->otrosIngresos4RetencionGanancias);
	$cells[2][4]->content = f($caja->cobranzaDeudores5RetencionIva + $caja->otrosIngresos5RetencionIva);
	$cells[2][5]->content = f($caja->cobranzaDeudores6RetencionIIBB + $caja->otrosIngresos6RetencionIIBB);
	$cells[2][6]->content = f($caja->totalIngresos);

	$rows[2]->class = 'bold';

	$htmlTabla = $tabla->create(true);
	$html .= $htmlTabla;
	return $htmlTabla;
}

function armarTablaEgresos(&$html, $caja) {
	$tabla = new HtmlTable(array('cantRows' => 4, 'cantCols' => 7, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	armoHead($tabla);

	$rows = array();
	$cells = array();
	$tabla->getRowCellArray($rows, $cells);
	for($i = 0; $i < $tabla->cantRows; $i++) {
		for($j = 0; $j < $tabla->cantCols; $j++) {
			$cells[$i][$j]->class = 'pRight5 pLeft5';
			if ($j == 0)
				$cells[$i][$j]->class .= ' bLeftDarkGray bRightDarkGray';
			else
				$cells[$i][$j]->class .= ' bRightDarkGray';
			if ($i == 3)
				$cells[$i][$j]->class .= ' bTopDarkGray bBottomDarkGray';
		}
	}

	$cells[0][0]->content = 'Depósitos bancarios';
	$cells[0][1]->content = f($caja->depositosBancarios1Efectivo);
	$cells[0][2]->content = f($caja->depositosBancarios2Cheque);
	$cells[0][3]->content = f(0);
	$cells[0][4]->content = f(0);
	$cells[0][5]->content = f(0);
	$cells[0][6]->content = f($caja->depositosBancariosTotal);

	$cells[1][0]->content = 'Órdenes de pago';
	$cells[1][1]->content = f($caja->ordenesDePago1Efectivo);
	$cells[1][2]->content = f($caja->ordenesDePago2Cheque);
	$cells[1][3]->content = f(0);
	$cells[1][4]->content = f(0);
	$cells[1][5]->content = f(0);
	$cells[1][6]->content = f($caja->ordenesDePagoTotal);

	$cells[2][0]->content = 'Gastos por rendición';
	$cells[2][1]->content = f($caja->gastosPorRendicion1Efectivo);
	$cells[2][2]->content = f(0);
	$cells[2][3]->content = f(0);
	$cells[2][4]->content = f(0);
	$cells[2][5]->content = f(0);
	$cells[2][6]->content = f($caja->gastosPorRendicionTotal);

	$cells[3][0]->content = 'Total egresos';
	$cells[3][1]->content = f($caja->depositosBancarios1Efectivo + $caja->ordenesDePago1Efectivo + $caja->gastosPorRendicion1Efectivo);
	$cells[3][2]->content = f($caja->depositosBancarios2Cheque + $caja->ordenesDePago2Cheque);
	$cells[3][3]->content = f(0);
	$cells[3][4]->content = f(0);
	$cells[3][5]->content = f(0);
	$cells[3][6]->content = f($caja->totalEgresos);

	$rows[3]->class = 'bold';

	$htmlTabla = $tabla->create(true);
	$html .= $htmlTabla;
	return $htmlTabla;
}

function cambiarSaldoAnterior(&$cajaAnterior, $caja) {
	$cajaAnterior->fecha = $caja->fecha;
	$cajaAnterior->importe1Efectivo += ($caja->cobranzaDeudores1Efectivo + $caja->otrosIngresos1Efectivo);
	$cajaAnterior->importe1Efectivo -= ($caja->depositosBancarios1Efectivo + $caja->ordenesDePago1Efectivo + $caja->gastosPorRendicion1Efectivo);
	$cajaAnterior->importe2Cheque += ($caja->cobranzaDeudores2Cheque + $caja->otrosIngresos2Cheque);
	$cajaAnterior->importe2Cheque -= ($caja->depositosBancarios2Cheque + $caja->ordenesDePago2Cheque);
	$cajaAnterior->importe4RetencionGanancias += ($caja->cobranzaDeudores4RetencionGanancias + $caja->otrosIngresos4RetencionGanancias);
	$cajaAnterior->importe5RetencionIva += ($caja->cobranzaDeudores5RetencionIva + $caja->otrosIngresos5RetencionIva);
	$cajaAnterior->importe6RetencionIIBB += ($caja->cobranzaDeudores6RetencionIIBB + $caja->otrosIngresos6RetencionIIBB);
	$cajaAnterior->importeTotal += ($caja->totalIngresos - $caja->totalEgresos);
	return $cajaAnterior;
}

function ponerBreak(&$html) {
	$html .= '<div class="pageBreak"></div>';
	return $html;
}

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');

try {
	comprobarFechas($desde, $hasta);

	$ultCajaCerr = Factory::getInstance()->getListObject('Caja', ' caja_cerrada = \'S\' ORDER BY fecha DESC', 1);
	$ultCajaCerr = $ultCajaCerr[0];
	if (Funciones::esFechaMenor($ultCajaCerr->fecha, $desde))
		$desde = Funciones::sumarTiempo($ultCajaCerr->fecha, 1, 'days');
	else {
		$ultCajaCerr = Factory::getInstance()->getListObject('Caja', ' caja_cerrada = \'S\' AND fecha < dbo.toDate(' . Datos::objectToDB($desde) . ') ORDER BY fecha DESC', 1);
		$ultCajaCerr = $ultCajaCerr[0];
	}
	$cajas = Factory::getInstance()->getListObject('Caja', ' (1 = 1)' . strFechas($desde, $hasta) . ' ORDER BY fecha ASC');

	//Arranco con las cajas y el reporte
	$html = '';
	$cajaAnterior = $ultCajaCerr;
	foreach ($cajas as $caja) {		
		//Armo tabla de Saldo anterior
		armarTablaSaldoAnterior($html, $cajaAnterior, $caja);
		
		//Armo tabla de Ingresos
		armarTablaIngresos($html, $caja);
		
		//Armo tabla de Egresos
		armarTablaEgresos($html, $caja);
		
		//Sumo/Resto la Caja anterior
		cambiarSaldoAnterior($cajaAnterior, $caja);
		
		//Armo tabla de Saldo final
		armarTablaSaldoAnterior($html, $cajaAnterior, $caja, true);

		//Si la caja actual está cerrada, la próxima Caja Anterior tiene que ser esta
		//Lo hago así porque puede diferir lo que da la función "cambiarSaldoAnterior" con lo grabado
		//entonces así vemos si hay diferencias y cajas mal cerradas
		if ($caja->cerrada == 'S')
			$cajaAnterior = $caja;

		//Pongo pageBreak
		ponerBreak($html);
	}

	echo $html;
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}




?>
<?php } ?>