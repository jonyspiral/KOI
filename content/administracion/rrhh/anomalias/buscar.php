<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/rrhh/anomalias/buscar/')) { ?>
<?php

function insertarFila(&$tabla, $fichajeActual) {
	// AGREGO LINEA
	$row = new HtmlTableRow();
	for ($j = 0; $j < $tabla->cantCols; $j++) {
		$cells[$j] = new HtmlTableCell();
		$cells[$j]->class .= (($j == 0) ? ' bLeftDarkGray bTopDarkGray bBottomDarkGray' : ' bTopDarkGray bBottomDarkGray') . (($j == 7) ? ' bRightDarkGray' : '');
	}
	$cells[0]->content = $fichajeActual->tipo;
	$cells[1]->content = $fichajeActual->personal->nombre;
	$cells[2]->content = $fichajeActual->personal->apellido;
	$cells[3]->content = Funciones::formatearFecha($fichajeActual->horaEntrada, 'H:i');
	$cells[4]->content = $fichajeActual->diferenciaEntrada;

	if ($fichajeActual->diferenciaEntrada > 60)
		$cells[4]->class = 'bLightRed bold bTopDarkGray bBottomDarkGray ';

	if ($fichajeActual->diferenciaEntrada <= 60 && $fichajeActual->diferenciaEntrada > 0)
		$cells[4]->class = 'bLightOrange bold bTopDarkGray bBottomDarkGray ';

	if ($fichajeActual->diferenciaEntrada <= 0)
		$cells[4]->class = 'bWhite bold bTopDarkGray bBottomDarkGray ';

	$cells[5]->content = Funciones::formatearFecha($fichajeActual->horaSalida, 'H:i');
	$cells[6]->content = $fichajeActual->diferenciaSalida;
	if ($fichajeActual->diferenciaSalida > 60)
		$cells[6]->class = 'bLightRed bold bTopDarkGray bBottomDarkGray ';

	if ($fichajeActual->diferenciaSalida <= 60 && $fichajeActual->diferenciaSalida > 0)
		$cells[6]->class = 'bLightOrange bold bTopDarkGray bBottomDarkGray ';

	if ($fichajeActual->diferenciaSalida <= 0)
		$cells[6]->class = 'bWhite bold bTopDarkGray bBottomDarkGray ';

	$cells[7]->content = restaHoras($fichajeActual->horaEntrada, $fichajeActual->horaSalida);
	// $cells[7]->class = 'bold bTopDarkGray bBottomDarkGray
	// brightDarkGray';

	for ($e = 12; $e < $tabla->cantCols; $e++) {
		$cells[$e]->class = 'bold bTopDarkGray bBottomDarkGray ';
	}

	for ($j = 0; $j < $tabla->cantCols; $j++) {
		$row->addCell($cells[$j]);
	}
	$tabla->addRow($row);
}

function insertarFilaP(&$tabla, $fichajeActual) {
	// AGREGO LINEA
	$row = new HtmlTableRow();
	for ($j = 0; $j < $tabla->cantCols; $j++) {
		$cells[$j] = new HtmlTableCell();
		if ($j == 0)
			$cells[$j]->class .= ' bLeftDarkGray bTopDarkGray bBottomDarkGray';
		else
			$cells[$j]->class .= ' bTopDarkGray bBottomDarkGray';
		if ($j == 8)
			$cells[$j]->class .= ' bRightDarkGray bTopDarkGray bBottomDarkGray';
	}
	$cells[0]->content = $fichajeActual->tipo;
	$cells[1]->content = $fichajeActual->fecha;
	$cells[2]->content = $fichajeActual->personal->nombre;
	$cells[3]->content = $fichajeActual->personal->apellido;
	$cells[4]->content = Funciones::formatearFecha($fichajeActual->horaEntrada, 'H:i');
	$cells[5]->content = $fichajeActual->diferenciaEntrada;

	if ($fichajeActual->diferenciaEntrada > 60)
		$cells[5]->class = 'bLightRed bold bTopDarkGray bBottomDarkGray ';

	if ($fichajeActual->diferenciaEntrada <= 60 && $fichajeActual->diferenciaEntrada > 0)
		$cells[5]->class = 'bLightOrange bold bTopDarkGray bBottomDarkGray ';

	if ($fichajeActual->diferenciaEntrada <= 0)
		$cells[5]->class = 'bWhite bold bTopDarkGray bBottomDarkGray ';

	$cells[6]->content = Funciones::formatearFecha($fichajeActual->horaSalida, 'H:i');
	$cells[7]->content = $fichajeActual->diferenciaSalida;
	if ($fichajeActual->diferenciaSalida > 60)
		$cells[7]->class = 'bLightRed bold bTopDarkGray bBottomDarkGray ';

	if ($fichajeActual->diferenciaSalida <= 60 && $fichajeActual->diferenciaSalida > 0)
		$cells[7]->class = 'bLightOrange bold bTopDarkGray bBottomDarkGray ';

	if ($fichajeActual->diferenciaSalida <= 0)
		$cells[7]->class = 'bWhite bold bTopDarkGray bBottomDarkGray ';

	$cells[8]->content =($fichajeActual->horaSalida != null) ? restaHoras($fichajeActual->horaEntrada, $fichajeActual->horaSalida): '00:00';
	// $cells[7]->class = 'bold bTopDarkGray bBottomDarkGray
	// brightDarkGray';

	for ($e = 12; $e < $tabla->cantCols; $e++) {
		$cells[$e]->class = 'bold bTopDarkGray bBottomDarkGray ';
	}

	for ($j = 0; $j < $tabla->cantCols; $j++) {
		$row->addCell($cells[$j]);
	}
	$tabla->addRow($row);
}

function restaHoras($entrada, $salida) {
	$dif = date("H:i", strtotime("00:00:00") + strtotime($salida) - strtotime($entrada));
	return $dif;
}

function armoHead(&$tabla) {
	// cornerL5
	$ths = array();
	$rows = array();
	// $tabla->getHeadArray($ths);
	for ($i = 0; $i < 2; $i++) {
		$rows[$i] = new HtmlTableRow();
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$ths[$i][$j] = new HtmlTableHead();
			if ($j == 0)
				$ths[$i][$j]->class = 'cornerL5';
			elseif (($j == $tabla->cantCols - 1) || ($i == 0 && $j == 7))
				$ths[$i][$j]->class = 'cornerR5 bLeftWhite';
			else
				$ths[$i][$j]->class = 'bLeftWhite';
		}
	}

	$tabla->headerClass('tableHeader');

	$ths[1][0]->content = 'Tipo';
	$ths[1][0]->dataType = 'Center';
	$ths[1][0]->style->width = '50px';
	$ths[1][1]->content = 'Nombre';
	$ths[1][1]->dataType = 'Left';
	$ths[1][1]->style->width = '100px';
	$ths[1][2]->content = 'Apellido';
	$ths[1][2]->dataType = 'Left';
	$ths[1][2]->style->width = '100px';
	$ths[1][3]->content = 'Hora ENT';
	$ths[1][3]->dataType = 'Center';
	$ths[1][3]->style->width = '50px';
	$ths[1][4]->content = 'Dif. ENT';
	$ths[1][4]->dataType = 'Center';
	$ths[1][4]->style->width = '50px';
	$ths[1][5]->content = 'Hora SAL';
	$ths[1][5]->dataType = 'Center';
	$ths[1][5]->style->width = '50px';
	$ths[1][6]->content = 'Dif. SAL';
	$ths[1][6]->dataType = 'Center';
	$ths[1][6]->style->width = '50px';
	$ths[1][7]->content = 'Hor. Trab';
	$ths[1][7]->dataType = 'Center';
	$ths[1][7]->style->width = '50px';

	for ($i = 0; $i < 2; $i++) {
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$rows[$i]->addCell($ths[$i][$j]);
		}
	}
	$tabla->addHeadRow($rows[1]);
	return $tabla;
}

function armoHeadP(&$tabla) {
	// cornerL5
	$ths = array();
	$rows = array();
	// $tabla->getHeadArray($ths);
	for ($i = 0; $i < 2; $i++) {
		$rows[$i] = new HtmlTableRow();
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$ths[$i][$j] = new HtmlTableHead();
			if ($j == 0)
				$ths[$i][$j]->class = 'cornerL5';
			elseif (($j == $tabla->cantCols - 1) || ($i == 0 && $j == 8))
				$ths[$i][$j]->class = 'cornerR5 bLeftWhite';
			else
				$ths[$i][$j]->class = 'bLeftWhite';
		}
	}

	$tabla->headerClass('tableHeader');

	$ths[1][0]->content = 'Tipo';
	$ths[1][0]->dataType = 'Center';
	$ths[1][0]->style->width = '100px';
	$ths[1][1]->content = 'Fecha';
	$ths[1][1]->dataType = 'Center';
	$ths[1][1]->style->width = '100px';
	$ths[1][2]->content = 'Nombre';
	$ths[1][2]->dataType = 'Left';
	$ths[1][2]->style->width = '150px';
	$ths[1][3]->content = 'Apellido';
	$ths[1][3]->dataType = 'Left';
	$ths[1][3]->style->width = '150px';
	$ths[1][4]->content = 'Hora ENT';
	$ths[1][4]->dataType = 'Center';
	$ths[1][4]->style->width = '75px';
	$ths[1][5]->content = 'Dif. ENT';
	$ths[1][5]->dataType = 'Center';
	$ths[1][5]->style->width = '75px';
	$ths[1][6]->content = 'Hora SAL';
	$ths[1][6]->dataType = 'Center';
	$ths[1][6]->style->width = '75px';
	$ths[1][7]->content = 'Dif. SAL';
	$ths[1][7]->dataType = 'Center';
	$ths[1][7]->style->width = '75px';
	$ths[1][8]->content = 'Hor. Trab';
	$ths[1][8]->dataType = 'Center';
	$ths[1][8]->style->width = '100px';

	for ($i = 0; $i < 2; $i++) {
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$rows[$i]->addCell($ths[$i][$j]);
		}
	}
	$tabla->addHeadRow($rows[1]);
	return $tabla;
}

function armarTablaFichaje(&$fichajes) {
	$htmlTabla = '';
	// Arranco con las cajas y el reporte
	/** @noinspection PhpUnusedLocalVariableInspection */
	$fichajeAnterior = 0;
	$x = 0;

	while ($x < count($fichajes)) {
		$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 8, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
		$fichajeActual = $fichajes[$x];
		$fechaActual = $fichajeActual->fecha;
		$fechaAnterior = $fechaActual;
		armoHead($tabla);
		$tabla->caption = $fechaActual;
		while ($fechaAnterior == $fechaActual) {
			if ($fichajeActual->fichajePosterior != null && $fichajeActual->tipo != 'REI') {
				$fichajePosterior = $fichajeActual->fichajePosterior;
				insertarFila($tabla, $fichajeActual);
				insertarFila($tabla, $fichajePosterior);
				while ($fichajePosterior->fichajePosterior != null) {
					insertarFila($tabla, $fichajePosterior->fichajePosterior);
					$fichajePosterior = $fichajePosterior->fichajePosterior;
				}
			} else {
				insertarFila($tabla, $fichajeActual);
			}
			// ***********************************************************************
			$x++;
			$fichajeActual = $fichajes[$x];
			$fechaActual = $fichajeActual->fecha;
		}

		$htmlTabla = $tabla->create(true);
	}
	return $htmlTabla;
}

function armarTablaFichajeP(&$fichajes) {
	$htmlTabla = '';
	// Arranco con las cajas y el reporte
	/** @noinspection PhpUnusedLocalVariableInspection */
	$fichajeAnterior = 0;
	$x = 0;
	
	while ($x < count($fichajes)) {
		$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 9, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
		$fichajeActual = $fichajes[$x];
		$personalActual = $fichajeActual->personal->apellido;
		$personalAnterior = $personalActual;
		armoHeadP($tabla);
		$tabla->caption = $fichajeActual->personal->apellido . ' - ' . $fichajeActual->personal->nombre;
		while ($personalAnterior == $personalActual) {
			if ($fichajeActual->fichajePosterior != null && $fichajeActual->tipo != 'REI') {
				$fichajePosterior = $fichajeActual->fichajePosterior;
				insertarFilaP($tabla, $fichajeActual);
				insertarFilaP($tabla, $fichajePosterior);
				while ($fichajePosterior->fichajePosterior != null) {
					insertarFilaP($tabla, $fichajePosterior->fichajePosterior);
					$fichajePosterior = $fichajePosterior->fichajePosterior;
				}
			} else {
				insertarFilaP($tabla, $fichajeActual);
			}
			// ***********************************************************************
			$x++;
			if (isset($fichajeActual->horaSalida) != null)
			$totalHorasTrabajadas += getMinutos(restaHoras($fichajeActual->horaEntrada, $fichajeActual->horaSalida));
			$fichajeActual = $fichajes[$x];
			$personalActual = $fichajeActual->personal->apellido;
		
		}
		$fila = new HtmlTableRow();
		for ($i = 0; $i <= 8; $i++) {
			$celdaAux[$i] = new HtmlTableCell();
			$fila->addCell($celdaAux[$i]);
		}
		$celdaAux[6]->content = 'Total horas trabajadas: ';
		$celdaAux[6]->style->width = '60px';
		$celdaAux[6]->colspan = '2';
		$celdaAux[8]->content = convertirMinutos($totalHorasTrabajadas);
		$celdaAux[8]->class = 'bLightOrange w70 bold bTopDarkGray bBottomDarkGray bold';
		$tabla->addRow($fila);
		$htmlTabla = $tabla->create(true);
	}
	return $htmlTabla;
}

function convertirMinutos($minutos) {
	$total = $minutos; // tiempo en minutos
	$horas = floor($total / 60);
	$minutos = $total % 60;
	return $horas . ":" . $minutos;
}

function getMinutos($hora) {
	$arr = explode(':', $hora);
	return Funciones::toInt($arr[0] * 60) + Funciones::toInt($arr[1]);
}

function comprobarFechas(&$desde, &$hasta) {
	$dias = 62;
	if (! isset($desde) && ! isset($hasta))
		throw new FactoryExceptionCustomException('Debe ingresar una fecha "desde" o una fecha "hasta"');

	if (! isset($desde))
		$desde = Funciones::sumarTiempo($hasta, -1 * $dias, 'days');
	if (! isset($hasta))
		$hasta = Funciones::sumarTiempo($desde, $dias, 'days');

	if (Funciones::esFechaMenor($hasta, $desde))
		throw new FactoryExceptionCustomException('La fecha "desde" no puede ser posterior a la fecha "hasta"');

	if (Funciones::diferenciaFechas($hasta, $desde) > $dias)
		throw new FactoryExceptionCustomException('El rango de fechas no puede superar los ' . $dias . ' días');
}

function strFechas($desde, $hasta) {
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

// GET*********************************************************************
$personal = Funciones::get('personal');
$seccion = Funciones::get('seccion');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$modo = Funciones::get('modo');

try {
	comprobarFechas($desde, $hasta);
	$sql = ' movimiento_tipo = ' . Datos::objectToDB('ENT') . (isset($personal) ? ' AND cod_personal = ' . Datos::objectToDB($personal) : '') . (isset($seccion) ? ' AND seccion_produccion = ' . Datos::objectToDB($seccion) : '');
	$sql .= ' ' . strFechas($desde, $hasta);
	$sql .= ($modo == 'F') ? ' ORDER BY fecha ASC' : ' ORDER BY legajo_nro ASC, fecha ASC';
	$fichajes = Factory::getInstance()->getListObject('Fichaje', $sql);
	if (count($fichajes) == 0) {
		throw new FactoryExceptionCustomException('No hay fichajes con ese filtro');
	}
	$html = ($modo == 'F') ? armarTablaFichaje($fichajes) : armarTablaFichajeP($fichajes);
	echo $html;
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>