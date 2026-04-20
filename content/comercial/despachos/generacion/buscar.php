<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/despachos/generacion/buscar/')) { ?>
<?php

function armoHead(&$tabla, $combinado) {
	$ths = array();
	$rows = array();
	$widths = array(25, 51, 10, 6, 8);
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
	$ths[0][0]->content = '<label>Observaciones: </label><input id="observaciones_' . $combinado . '" class="textbox w600" />';

	$ths[1][0]->content = 'Artículo';
	$ths[1][0]->dataType = 'Center';
	$ths[1][1]->content = 'Cantidades';
	$ths[1][2]->content = 'F. predespacho';
	$ths[1][2]->dataType = 'Fecha';
	$ths[1][3]->content = 'Pares';
	$ths[1][3]->dataType = 'Entero';
	$ths[1][4]->content = 'Despachar';
	$ths[1][4]->dataType = 'Center';

	for ($i = 0; $i < 2; $i++)
		for ($j = 0; $j < $tabla->cantCols; $j++)
			$rows[$i]->addCell($ths[$i][$j]);

	$tabla->addHeadRow($rows[0]);
	$tabla->addHeadRow($rows[1]);

	return $tabla;
}

function divTablita($item){
	$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 8, 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->getHeadArray($ths);
	$tabla->getRowCellArray($rows, $cells);

	$stock = $item->colorPorArticulo->getStockAlmacen($item->idAlmacen);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$talle = $item->articulo->rangoTalle->posicion[$i + 1];
		$ths[$i]->content = ($talle ? $talle : '-');
		$ths[$i]->class =  ($i % 2 ? 'w12p' : 'w13p');
		/*
		$cells[0][$i]->content = ($talle ? $item->predespachados[$i + 1] : '-');
		$cells[0][$i]->class = 'aCenter';
		*/
		$comboClass = $item->pedidoNumero . '-' . $item->pedidoNumeroDeItem;
		$comboId = $item->pedidoNumero . '-' . $item->pedidoNumeroDeItem . '-' . ($i + 1);
		$cant = ($talle ? $item->predespachados[$i + 1] : 0);
		$cells[0][$i]->content = ($talle ? ('<input id="par_' . $comboId . '" class="textbox w20 aCenter inputPar par_' . $comboClass . '" maxCant="' . $cant . '" type="text" validate="Entero" value="' . $cant . '" />') . '<label class="pLeft5">(' . $stock[$i + 1] . ')</label>' : '-');
		$cells[0][$i]->class = 'aCenter';
	}
	$tabla->headerClass('tableHeader');

	$html = '<div id="divTablita_' . $item->pedidoNumero . '-' . $item->pedidoNumeroDeItem . '" class="divTablita">' . $tabla->create(true) . '</div>';
	return $html;
}

function meterItem(&$tabla, $item) {
	/** @var Predespacho $item */
	$combItem = $item->pedidoNumero . '-' . $item->pedidoNumeroDeItem;
	$row = new HtmlTableRow();
	for($j = 0; $j < $tabla->cantCols; $j++) {
		$cells[$j] = new HtmlTableCell();
		$cells[$j]->class = 'pRight5 pLeft5';
	}
	$cells[0]->content = '[' . $item->idAlmacen . '-' . $item->idArticulo . '-' . $item->idColorPorArticulo . ']<br>' . $item->articulo->nombre . ' - ' . $item->colorPorArticulo->nombre;
	$cells[1]->content = divTablita($item);
	$cells[2]->content = $item->fechaAlta;
	$cells[3]->content = $item->getTotalPredespachados();
	$combinado = $item->pedido->cliente->id . '-' . $item->pedido->sucursal->id;
	$cells[3]->id = 'pares_chk_' . $combItem;
	$cells[3]->class = 'aCenter pares_chkTodos_' . $combinado;
	$cells[4]->content = '<input type="checkbox" id="chk_' . $combItem . '" class="chkUno ' . $combinado . '" />';
	for($j = 0; $j < $tabla->cantCols; $j++)
		$row->addCell($cells[$j]);
	$row->class = $combItem;
	$tabla->addRow($row);
}

$idCliente = Funciones::get('idCliente');
//$empresa = Funciones::session('empresa');
$almacen = Funciones::get('almacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	$html = '';
	$where = '(predespachados > 0) AND (anulado = \'N\') ';
	$where .= ' AND (cod_ecommerce_order IS NULL)';
	if ($idCliente) $where .= ' AND (cod_cliente = ' . Datos::objectToDB($idCliente) . ')';
	//if ($empresa) //$where .= ' AND (empresa = ' . Datos::objectToDB($empresa) . ')';
	if ($almacen) $where .= ' AND (cod_almacen = ' . Datos::objectToDB($almacen) . ')';
	if ($idArticulo) $where .= ' AND (cod_articulo = ' . Datos::objectToDB($idArticulo) . ')';
	if ($idColor) $where .= ' AND (cod_color = ' . Datos::objectToDB($idColor) . ')';
	$where .= ' AND (';
	for ($i = 1; $i < 10; $i++)
		$where .= 'pred_' . $i . ' <> 0 OR ';
	$where .= 'pred_10 <> 0) ';
	$order = ' ORDER BY razon_social ASC, cod_cliente ASC, cod_sucursal ASC, empresa ASC, fecha_alta DESC';

	$items = Factory::getInstance()->getListObject('Predespacho', $where . $order);
	if (count($items) == 0)
		throw new FactoryExceptionCustomException('No hay predespachos pendientes con ese filtro');

	$i = 0;
	$j = 0;
	$combinadoActual = '';
	$combinadoAnterior = '';
	while ($i < count($items)) {
		$item = $items[$i];

		$cli = $item->pedido->cliente;
		$suc = $item->pedido->sucursal;
		$combinadoActual = $cli->id . '-' . $suc->id;
		$combinadoAnterior = $cli->id . '-' . $suc->id;

		//CREO TABLA NUEVA
		$tabla = new HtmlTable(array('cantCols' => 5, 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%'));
		armoHead($tabla, $combinadoAnterior);

		$pares = 0;
		while($combinadoAnterior == $combinadoActual) {
			//METO ITEM
			meterItem($tabla, $item);
			$pares += $item->getTotalPredespachados();
			//PASO AL PRÓX ITEM
			$j++;
			if ($j < count($items)) {
				$item = $items[$j];
				$combinadoActual = $item->pedido->idCliente . '-' . $item->pedido->idSucursal;
			} else
				$combinadoActual = '';
		}
		//CIERRO TABLA
		$echo = '<div id="divPredespacho_' . $combinadoAnterior . '" idCli="' . $idCli . '" idSuc="' . $idSuc . '">
		<div>' . $cli->razonSocial . ' - ' . $suc->nombre . ' - Pares: ' . $pares . '<div class="fRight pRight10">Pares a despachar: <span id="spanCantidadParesDespachar_' . $combinadoAnterior . '" class="spanCantidadPares">0</span><input type="checkbox" class="chkTodos" id="chkTodos_' . $combinadoAnterior . '" /></div></div>
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