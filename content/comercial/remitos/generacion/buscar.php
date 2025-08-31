<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/remitos/generacion/buscar/')) { ?>
<?php

function armoHead(&$tabla, $combinado) {
	$ths = array();
	$rows = array();
	$widths = array(3, 8, 48, 9, 18, 6, 8);
	for ($i = 0; $i < 2; $i++) {
		$rows[$i] = new HtmlTableRow();
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$ths[$i][$j] = new HtmlTableHead();
			if ($i == 1) {
				$ths[$i][$j]->style->width = $widths[$j] . '%';
				if ($j == 0) $ths[$i][$j]->class = 'cornerL5';
				elseif ($j == $tabla->cantCols - 1) $ths[$i][$j]->class = 'cornerR5 bLeftWhite';
				else $ths[$i][$j]->class = 'bLeftWhite';
			}
		}
	}

	$tabla->headerClass('tableHeader');

	$ths[0][0]->colspan = 5;
	$ths[0][0]->style->color = 'black';
	$ths[0][0]->style->background_color = 'white';
	$ths[0][0]->style->font_weight = 'normal';
	$ths[0][0]->style->text_align = 'left';
	$ths[0][0]->content = '<label>Observaciones: </label><input id="observaciones_' . $combinado . '" class="textbox w580" />';
	$ths[0][5]->colspan = 2;
	$ths[0][5]->style->color = 'black';
	$ths[0][5]->style->background_color = 'white';
	$ths[0][5]->style->font_weight = 'normal';
	$ths[0][5]->style->text_align = 'right';
	$ths[0][5]->content = '<label>Bultos: </label><input id="bultos_' . $combinado . '" class="textbox w30" maxlength="3" validate="Entero" />';

	$ths[1][0]->content = '';
	$ths[1][0]->dataType = 'Center';
	$ths[1][1]->content = 'Amc.';
	$ths[1][1]->dataType = 'Center';
	$ths[1][2]->content = 'Artículo';
	$ths[1][3]->content = 'Color';
	$ths[1][3]->dataType = 'Center';
	$ths[1][4]->content = 'Fecha desp.';
	$ths[1][4]->dataType = 'Center';
	$ths[1][5]->content = 'Pares';
	$ths[1][5]->dataType = 'Entero';
	$ths[1][6]->content = 'Remitir';
	$ths[1][6]->dataType = 'Center';
	

	for ($i = 0; $i < 2; $i++)
		for ($j = 0; $j < $tabla->cantCols; $j++)
			$rows[$i]->addCell($ths[$i][$j]);

	$tabla->addHeadRow($rows[0]);
	$tabla->addHeadRow($rows[1]);
	
	return $tabla;
}

function divTablita($item){
	/** @var DespachoItem $item */
	$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 8, 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->getHeadArray($ths);
	$tabla->getRowCellArray($rows, $cells);

	$stock = $item->colorPorArticulo->getStockAlmacen($item->idAlmacen);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$talle = $item->articulo->rangoTalle->posicion[$i + 1];
		$ths[$i]->content = ($talle ? $talle : '-');
		$ths[$i]->class =  ($i % 2 ? 'w12p' : 'w13p');
		$cells[0][$i]->content = ($talle ? $item->cantidad[$i + 1] . ' (' . $stock[$i + 1] . ')' : '-');
		$cells[0][$i]->class = 'aCenter';
	}
	$tabla->headerClass('tableHeader');

	$html = '<div id="divTablita_' . $item->despachoNumero . '-' . $item->numeroDeItem . '" class="divTablita" style="display: none; ">' . $tabla->create(true) . '</div>';
	return $html;
}

function meterItem(&$tabla, $item) {
	$row = new HtmlTableRow();
	for($j = 0; $j < $tabla->cantCols; $j++) {
		$cells[$j] = new HtmlTableCell();
		$cells[$j]->class = 'pRight5 pLeft5';
	}
	$cells[0]->content = '+';
	$cells[0]->class = 'toggleTablita cPointer';
	$cells[1]->content = $item->idAlmacen;
	$cells[2]->content = '<div><label>' . $item->idArticulo . ' - ' . $item->articulo->nombre . '</label></div>';
	$cells[2]->content .= divTablita($item);
	$cells[3]->content = $item->idColorPorArticulo;
	$cells[4]->content = $item->fechaAlta;
	$cells[5]->content = $item->cantidadTotal;
	$combinado = $item->cliente->id . '-' . $item->sucursal->id . '-' . $item->empresa;
	$cells[5]->id = 'pares_chk_' . $item->despachoNumero . '-' . $item->numeroDeItem;
	$cells[5]->class = 'aCenter pares_chkTodos_' . $combinado;
	$cells[6]->content = '<input type="checkbox" id="chk_' . $item->despachoNumero . '-' . $item->numeroDeItem . '" class="chkUno ' . $combinado . '" />';
	for($j = 0; $j < $tabla->cantCols; $j++)
		$row->addCell($cells[$j]);
	$row->class = $item->despachoNumero . '-' . $item->numeroDeItem;
	$tabla->addRow($row);
}

$empresa = Funciones::session('empresa');
$idCliente = Funciones::get('idCliente');
$almacen = Funciones::get('almacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	$html = '';
	$where = '(empresa = ' . Datos::objectToDB($empresa) . ')';
	$where .= ' AND (cod_ecommerce_order IS NULL)';
	if ($idCliente)
		$where .= ' AND (cod_cliente = ' . Datos::objectToDB($idCliente) . ')';
	if ($almacen)
		$where .= ' AND (cod_almacen = ' . Datos::objectToDB($almacen) . ')';
	if ($idArticulo)
		$where .= ' AND (cod_articulo = ' . Datos::objectToDB($idArticulo) . ')';
	if ($idColor)
		$where .= ' AND (cod_color = ' . Datos::objectToDB($idColor) . ')';
	$where = trim($where, ' AND ') . ($where ? ' AND ' : '');
	$where .= ' (anulado = \'N\' OR anulado IS NULL) AND (nro_remito IS NULL) AND (';
	for ($i = 1; $i < 10; $i++)
		$where .= 'cant_' . $i . ' <> 0 OR ';
	$where .= 'cant_10 <> 0) ';
	$order = ' ORDER BY razon_social ASC, cod_cliente ASC, cod_sucursal ASC, empresa ASC, fecha_alta DESC';

	$items = Factory::getInstance()->getListObject('DespachoItem', $where . $order);
	if (count($items) == 0)
		throw new FactoryExceptionCustomException('No hay despachos pendientes con ese filtro');

	$i = 0;
	$j = 0;
	$combinadoActual = '';
	$combinadoAnterior = '';
	while ($i < count($items)) {
		$item = $items[$i];

		$cli = $item->cliente;
		$suc = $item->sucursal;
		$emp = $item->empresa;
		$combinadoActual = $cli->id . '-' . $suc->id . '-' . $emp;
		$combinadoAnterior = $cli->id . '-' . $suc->id . '-' . $emp;

		//CREO TABLA NUEVA
		$tabla = new HtmlTable(array('cantCols' => 7, 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%'));
		armoHead($tabla, $combinadoAnterior);

		$pares = 0;
		while($combinadoAnterior == $combinadoActual) {
			//METO ITEM
			meterItem($tabla, $item);
			$pares += $item->cantidadTotal;
			//PASO AL PRÓX ITEM
			$j++;
			if ($j < count($items)) {
				$item = $items[$j];
				$combinadoActual = $item->idCliente . '-' . $item->idSucursal . '-' . $item->empresa;
			} else
				$combinadoActual = '';
		}
		//CIERRO TABLA
		$echo = '<div id="divDespacho_' . $combinadoAnterior . '" idCli="' . $idCli . '" idSuc="' . $idSuc . '" idEmp="' . $idEmp . '">
		<div>' . $cli->razonSocial . ' - ' . $suc->nombre . ' - ' . $emp . ' - Pares: ' . $pares . '<div class="fRight pRight10">Pares a remitir: <span id="spanCantidadParesRemitir_' . $combinadoAnterior . '" class="spanCantidadPares">0</span><input type="checkbox" class="chkTodos" id="chkTodos_' . $combinadoAnterior . '" /></div></div>
		<div>' . $tabla->create(true) . '</div>
		</div>';
		$html .= $echo;
		$i = $j;
	}

	echo $html;
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}
?>
<?php } ?>