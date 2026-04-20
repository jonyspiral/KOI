<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/pendientes/buscar/')) { ?>
<?php

function armoHead(HtmlTable &$tabla) {
	//cornerL5
	$ths = array();
	$rows = array();
	//$tabla->getHeadArray($ths);
	for ($i = 0; $i < 2; $i++) {
		$rows[$i] = new HtmlTableRow();
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$ths[$i][$j] = new HtmlTableHead();
			if ($j == 0) $ths[$i][$j]->class = 'cornerL5';
			elseif (($j == $tabla->cantCols - 1) || ($i == 0 && $j ==13)) $ths[$i][$j]->class = 'cornerR5 bLeftWhite';
			else $ths[$i][$j]->class = 'bLeftWhite';
		}
	}

	$tabla->headerClass('tableHeader');
	
	$ths[0][0]->colspan = 3	;
	$ths[0][3]->colspan = 8;
	$ths[0][3]->content = 'Original';
	$ths[0][12]->colspan = 8;
	$ths[0][12]->content = 'Pendientes';
	$ths[1][0]->content = 'Artículo';
	$ths[1][0]->class = 'w500';
	$ths[1][1]->content = 'Color';
	$ths[1][2]->content = 'Rango';
	$ths[1][2]->dataType = 'center';	
	$ths[1][3]->content = 'C1';
	$ths[1][3]->dataType = 'Entero';
	$ths[1][4]->content = 'C2';
	$ths[1][4]->dataType = 'Entero';
	$ths[1][5]->content = 'C3';
	$ths[1][5]->dataType = 'Entero';
	$ths[1][6]->content = 'C4';
	$ths[1][6]->dataType = 'Entero';
	$ths[1][7]->content = 'C5';
	$ths[1][7]->dataType = 'Entero';
	$ths[1][8]->content = 'C6';
	$ths[1][8]->dataType = 'Entero';
	$ths[1][9]->content = 'C7';
	$ths[1][9]->dataType = 'Entero';
	$ths[1][10]->content = 'C8';
	$ths[1][10]->dataType = 'Entero';
	$ths[1][11]->content = 'CTot';
	$ths[1][11]->dataType = 'Entero';
	$ths[1][12]->content = 'P1';
	$ths[1][12]->dataType = 'Entero';
	$ths[1][13]->content = 'P2';
	$ths[1][13]->dataType = 'Entero';
	$ths[1][14]->content = 'P3';
	$ths[1][14]->dataType = 'Entero';
	$ths[1][15]->content = 'P4';
	$ths[1][15]->dataType = 'Entero';
	$ths[1][16]->content = 'P5';
	$ths[1][16]->dataType = 'Entero';
	$ths[1][17]->content = 'P6';
	$ths[1][17]->dataType = 'Entero';
	$ths[1][18]->content = 'P7';
	$ths[1][18]->dataType = 'Entero';
	$ths[1][19]->content = 'P8';
	$ths[1][19]->dataType = 'Entero';
	$ths[1][20]->content = 'PTot';
	$ths[1][20]->dataType = 'Entero';
	
	for ($i = 0; $i < 2; $i++) {
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$rows[$i]->addCell($ths[$i][$j]);
		}
	}
	
	$tabla->addHeadRow($rows[0]);
	$tabla->addHeadRow($rows[1]);
	
	return $tabla;
}

function armarTablaPendientes(&$html, &$items) {	
	//Arranco con las cajas y el reporte
	/** @noinspection PhpUnusedLocalVariableInspection */
	$pedidoAnterior = 0;
	$x = 0;
	$z = 0;
	$total = 0;
	while ($x < count($items)) {
		$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 21, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
		
		$item = $items[$x];
		
		//CREO TABLA NUEVA

		// +LOOP
		$pedidoAnterior = $item->numero;
		armoHead($tabla);
		$caption = '';
		$caption .= '[';
		$caption .= $item->pedido->cliente->id;
		$caption .= '] ';
		$caption .= $item->pedido->cliente->razonSocial;
		$caption .= ' - '.$item->pedido->sucursal->nombre;
		$caption .= ' - Ped: '.$pedidoAnterior.' - ';
		$caption .= $item->pedido->fechaAlta;
		$caption .= ' Pend: '. ($item->pedido->paresPendientes + $item->pedido->paresPredespachados);
		$tabla->caption = $caption;
		$tabla->captionClass ='s20';
		//***********************************************************************
		while ($pedidoAnterior == $item->numero) {
			//AGREGO LINEA
			$row = new HtmlTableRow();
			for($j = 0; $j < $tabla->cantCols; $j++) {
				$cells[$j] = new HtmlTableCell();
				$cells[$j]->class = 'pRight5 pLeft5';
				if ($j == 0)
					$cells[$j]->class .= ' bAllDarkGray';
				else
					$cells[$j]->class .= ' bTopDarkGray bBottomDarkGray bRightDarkGray';
			}
			
			$cells[0]->content = $item->idArticulo;
			$cells[0]->content .= '-';
			$cells[0]->content .= $item->articulo->nombre;
			$cells[1]->content = $item->idColorPorArticulo;	
			$cells[2]->content = $item->articulo->rangoTalle->posicionInicial.' - '.$item->articulo->rangoTalle->posicionFinal;		
			$cells[3]->content = $item->cantidad[1];
			$cells[4]->content = $item->cantidad[2];
			$cells[5]->content = $item->cantidad[3];
			$cells[6]->content = $item->cantidad[4];
			$cells[7]->content = $item->cantidad[5];
			$cells[8]->content = $item->cantidad[6];
			$cells[9]->content = $item->cantidad[7];
			$cells[10]->content = $item->cantidad[8];
			$cells[11]->content = $item->getTotalCantidad();
			$cells[12]->content = $item->pendiente[1] + $item->predespachados[1] + $item->tickeados[1];			
			$cells[13]->content = $item->pendiente[2] + $item->predespachados[2] + $item->tickeados[2];			
			$cells[14]->content = $item->pendiente[3] + $item->predespachados[3] + $item->tickeados[3];			
			$cells[15]->content = $item->pendiente[4] + $item->predespachados[4] + $item->tickeados[4];			
			$cells[16]->content = $item->pendiente[5] + $item->predespachados[5] + $item->tickeados[5];			
			$cells[17]->content = $item->pendiente[6] + $item->predespachados[6] + $item->tickeados[6];			
			$cells[18]->content = $item->pendiente[7] + $item->predespachados[7] + $item->tickeados[7];			
			$cells[19]->content = $item->pendiente[8] + $item->predespachados[8] + $item->tickeados[8];
			$cells[20]->content = $item->getTotalPendiente() + $item->getTotalPredespachados() + $item->getTotalTickeados();
			$importePendientes += $item->getImportePendiente() + $item->getImporteTickeado() + $item->getImportePredespachado();
			$totalCantidad += $item->getTotalCantidad();
			$total += $item->getTotalPendiente() + $item->getTotalPredespachados() + $item->getTotalTickeados();
			for($e = 12; $e < $tabla->cantCols; $e++) {
				$cells[$e]->class ='bold bTopDarkGray bBottomDarkGray bRightDarkGray';
			}
	
			for($j = 0; $j < $tabla->cantCols; $j++) {
				$row->addCell($cells[$j]);
			}
			$tabla->addRow($row);
			
			$z++;
			if ($z < count($items)){
				$item = $items[$z];
			}else{
				$pedidoAnterior = 0;
			}
			
		}
		// -LOOP
		//CIERRO TABLA
		$htmlTabla = $tabla->create(true);
		$html .= $htmlTabla;
		$x = $z;
		
	}
	$tabla2 = new HtmlTable(array('cantRows' => 3, 'cantCols' => 1, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tabla2->getRowCellArray($rows, $cells);
	$cells[0][0]->content ='</br><label>Cantidad de pares pedidos: ' . $totalCantidad . '</label>';
	$rows[0]->class = 'bold';
	$cells[1][0]->content ='</br><label>Cantidad de pares pendientes: ' . $total . '</label>';
	$rows[1]->class = 'bold';
	$cells[2][0]->content ='</br><label>Importe total pendientes (estimado): ' . Funciones::formatearMoneda($importePendientes) . '</label>';
	$rows[2]->class = 'bold';
	
	$htmlTabla2 = $tabla2->create(true);
	$html .= $htmlTabla2;
	return $htmlTabla;
}

function armoHeadPorArticulo(HtmlTable &$tabla) {
	//cornerL5
	$ths = array();
	$rows = array();
	//$tabla->getHeadArray($ths);
	for ($i = 0; $i < 2; $i++) {
		$rows[$i] = new HtmlTableRow();
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$ths[$i][$j] = new HtmlTableHead();
			if ($j == 0) $ths[$i][$j]->class = 'cornerL5';
			elseif (($j == $tabla->cantCols - 1) || ($i == 0 && $j ==13)) $ths[$i][$j]->class = 'cornerR5 bLeftWhite';
			else $ths[$i][$j]->class = 'bLeftWhite';
		}
	}

	$tabla->headerClass('tableHeader');

	$ths[0][0]->colspan = 2	;
	$ths[0][2]->colspan = 8;
	$ths[0][2]->content = 'Original';
	$ths[0][11]->colspan = 8;
	$ths[0][11]->content = 'Pendientes';

	$ths[1][0]->content = 'Cliente';
	$ths[1][0]->class = 'w500';
	$ths[1][1]->content = 'Rango';
	$ths[1][1]->dataType = 'center';
	$ths[1][2]->content = 'C1';
	$ths[1][2]->dataType = 'Entero';
	$ths[1][3]->content = 'C2';
	$ths[1][3]->dataType = 'Entero';
	$ths[1][4]->content = 'C3';
	$ths[1][4]->dataType = 'Entero';
	$ths[1][5]->content = 'C4';
	$ths[1][5]->dataType = 'Entero';
	$ths[1][6]->content = 'C5';
	$ths[1][6]->dataType = 'Entero';
	$ths[1][7]->content = 'C6';
	$ths[1][7]->dataType = 'Entero';
	$ths[1][8]->content = 'C7';
	$ths[1][8]->dataType = 'Entero';
	$ths[1][9]->content = 'C8';
	$ths[1][9]->dataType = 'Entero';
	$ths[1][10]->content = 'CTot';
	$ths[1][10]->dataType = 'Entero';
	$ths[1][11]->content = 'P1';
	$ths[1][11]->dataType = 'Entero';
	$ths[1][12]->content = 'P2';
	$ths[1][12]->dataType = 'Entero';
	$ths[1][13]->content = 'P3';
	$ths[1][13]->dataType = 'Entero';
	$ths[1][14]->content = 'P4';
	$ths[1][14]->dataType = 'Entero';
	$ths[1][15]->content = 'P5';
	$ths[1][15]->dataType = 'Entero';
	$ths[1][16]->content = 'P6';
	$ths[1][16]->dataType = 'Entero';
	$ths[1][17]->content = 'P7';
	$ths[1][17]->dataType = 'Entero';
	$ths[1][18]->content = 'P8';
	$ths[1][18]->dataType = 'Entero';
	$ths[1][19]->content = 'PTot';
	$ths[1][19]->dataType = 'Entero';

	for ($i = 0; $i < 2; $i++) {
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$rows[$i]->addCell($ths[$i][$j]);
		}
	}

	$tabla->addHeadRow($rows[0]);
	$tabla->addHeadRow($rows[1]);

	return $tabla;
}

function armarTablaPendientesPorArticulo(&$html, &$items) {
	$pedidoAnterior = 0;
	$x = 0;
	$z = 0;
	$total = 0;
	while ($x < count($items)) {
		$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 20, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));

		$item = $items[$x];
		/** @var PedidoItem $item */

		//CREO TABLA NUEVA

		// +LOOP
		$pedidoAnterior = $item->colorPorArticulo->articulo->id . '_' . $item->colorPorArticulo->id;
		armoHeadPorArticulo($tabla);
		$caption = '';
		$caption .= '[';
		$caption .= $item->colorPorArticulo->articulo->id;
		$caption .= '] ';
		$caption .= $item->colorPorArticulo->articulo->nombre;
		$caption .= ' - Color: ' . $item->colorPorArticulo->id;

		$tabla->caption = $caption;
		$tabla->captionClass ='s20';
		//***********************************************************************
		while ($pedidoAnterior == ($item->colorPorArticulo->articulo->id . '_' . $item->colorPorArticulo->id)) {
			//AGREGO LINEA
			$row = new HtmlTableRow();
			for($j = 0; $j < $tabla->cantCols; $j++) {
				$cells[$j] = new HtmlTableCell();
				$cells[$j]->class = 'pRight5 pLeft5';
				if ($j == 0)
					$cells[$j]->class .= ' bAllDarkGray';
				else
					$cells[$j]->class .= ' bTopDarkGray bBottomDarkGray bRightDarkGray';
			}

			$cells[0]->content = $item->pedido->cliente->getIdNombre();
			$cells[1]->content = $item->articulo->rangoTalle->posicionInicial.' - '.$item->articulo->rangoTalle->posicionFinal;
			$cells[2]->content = $item->cantidad[1];
			$cells[3]->content = $item->cantidad[2];
			$cells[4]->content = $item->cantidad[3];
			$cells[5]->content = $item->cantidad[4];
			$cells[6]->content = $item->cantidad[5];
			$cells[7]->content = $item->cantidad[6];
			$cells[8]->content = $item->cantidad[7];
			$cells[9]->content = $item->cantidad[8];
			$cells[10]->content = $item->getTotalCantidad();
			$cells[11]->content = $item->pendiente[1] + $item->predespachados[1] + $item->tickeados[1];
			$cells[12]->content = $item->pendiente[2] + $item->predespachados[2] + $item->tickeados[2];
			$cells[13]->content = $item->pendiente[3] + $item->predespachados[3] + $item->tickeados[3];
			$cells[14]->content = $item->pendiente[4] + $item->predespachados[4] + $item->tickeados[4];
			$cells[15]->content = $item->pendiente[5] + $item->predespachados[5] + $item->tickeados[5];
			$cells[16]->content = $item->pendiente[6] + $item->predespachados[6] + $item->tickeados[6];
			$cells[17]->content = $item->pendiente[7] + $item->predespachados[7] + $item->tickeados[7];
			$cells[18]->content = $item->pendiente[8] + $item->predespachados[8] + $item->tickeados[8];
			$cells[19]->content = $item->getTotalPendiente() + $item->getTotalPredespachados() + $item->getTotalTickeados();
			$importePendientes += $item->getImportePendiente() + $item->getImporteTickeado() + $item->getImportePredespachado();
			$totalCantidad += $item->getTotalCantidad();
			$total += $item->getTotalPendiente() + $item->getTotalPredespachados() + $item->getTotalTickeados();
			for($e = 12; $e < $tabla->cantCols; $e++) {
				$cells[$e]->class ='bold bTopDarkGray bBottomDarkGray bRightDarkGray';
			}

			for($j = 0; $j < $tabla->cantCols; $j++) {
				$row->addCell($cells[$j]);
			}
			$tabla->addRow($row);

			$z++;
			if ($z < count($items)){
				$item = $items[$z];
			}else{
				$pedidoAnterior = 0;
			}

		}
		// -LOOP
		//CIERRO TABLA
		$htmlTabla = $tabla->create(true);
		$html .= $htmlTabla;
		$x = $z;

	}
	$tabla2 = new HtmlTable(array('cantRows' => 3, 'cantCols' => 1, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tabla2->getRowCellArray($rows, $cells);
	$cells[0][0]->content ='</br><label>Cantidad de pares pedidos: ' . $totalCantidad . '</label>';
	$rows[0]->class = 'bold';
	$cells[1][0]->content ='</br><label>Cantidad de pares pendientes: ' . $total . '</label>';
	$rows[1]->class = 'bold';
	$cells[2][0]->content ='</br><label>Importe total pendientes (estimado): ' . Funciones::formatearMoneda($importePendientes) . '</label>';
	$rows[2]->class = 'bold';

	$htmlTabla2 = $tabla2->create(true);
	$html .= $htmlTabla2;
	return $htmlTabla;
}

//GET*********************************************************************
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$cliente = Funciones::get('cliente');
$vendedor = Funciones::get('vendedor');
$articulo = Funciones::get('articulo');
$color = Funciones::get('color');
$esPorArtículo = !empty($articulo);
//POST********************************************************************

try {
	 if (Usuario::logueado()->esCliente())
	 	$cliente = Usuario::logueado()->contacto->cliente->id;
	 if (Usuario::logueado()->esVendedor())
	 	$vendedor = Usuario::logueado()->codigoPersonal;

	$sql = 'anulado = ' . Datos::objectToDB('N') . ' AND (pendiente > 0 OR predespachados > 0) AND ' ;
	$sql .= Funciones::strFechas($desde, $hasta, 'fecha_pedido');
	$sql = trim($sql, ' AND ');
	$sql .= (isset($cliente) ? ' AND cod_cliente = ' . Datos::objectToDB($cliente) : '');
	$sql .= (isset($articulo) ? ' AND cod_articulo = ' . Datos::objectToDB($articulo) : '');
	$sql .= (isset($color) ? ' AND cod_color_articulo = ' . Datos::objectToDB($color) : '');
	$sql .= (isset($vendedor) ? ' AND cod_vendedor = ' . Datos::objectToDB($vendedor) : '');
	$sql .= ' ORDER BY ' . ($esPorArtículo ? 'cod_articulo ASC, cod_color_articulo ASC' : 'nro_pedido ASC');

	//Arranco con cosas
	$itemsPendientes = Factory::getInstance()->getListObject('PedidoItem', $sql);
	$html = '';
	if (count($itemsPendientes) == 0)
		throw new FactoryException('No existen pedidos pendientes con ese filtro');

	if($esPorArtículo) {
		armarTablaPendientesPorArticulo($html, $itemsPendientes);
	} else {
		armarTablaPendientes($html, $itemsPendientes);
	}
	echo $html;

} catch (FactoryException $ex) {
	Html::jsonInfo($ex->getMessage());
}catch (FactoryExceptionRegistroNoExistente $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

 } ?>