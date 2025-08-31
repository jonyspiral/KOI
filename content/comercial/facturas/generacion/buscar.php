<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/facturas/generacion/buscar/')) { ?>
<?php

function armoHead(&$tabla) {
	$tabla->getHeadArray($ths);
	
	for ($j = 0; $j < $tabla->cantCols; $j++) {
		if ($j == 0) $ths[$j]->class = 'cornerL5';
		elseif ($j == $tabla->cantCols - 1) $ths[$j]->class = 'cornerR5 bLeftWhite';
		else $ths[$j]->class = 'bLeftWhite';
	}

	
	$tabla->headerClass('tableHeader');

	$ths[0]->content = 'Amc.';
	$ths[0]->dataType = 'Center';
	$ths[1]->content = 'Artículo';
	$ths[2]->content = 'Color';
	$ths[2]->dataType = 'Center';
	$ths[3]->content = 'Pares';
	$ths[3]->dataType = 'Center';
	$ths[4]->content = 'Detalle';
	$ths[4]->dataType = 'Center';
}

function divTablita($item){
	$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 8, 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->getHeadArray($ths);
	$tabla->getRowCellArray($rows, $cells);

	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$talle = $item->articulo->rangoTalle->posicion[$i + 1];
		$ths[$i]->content = ($talle ? $talle : '-');
		$ths[$i]->class =  ($i % 2 ? 'w12p' : 'w13p');
		$cells[0][$i]->content = ($talle ? $item->cantidad[$i + 1] : '-');
		$cells[0][$i]->class = 'aCenter';
	}
	$tabla->headerClass('tableHeader');

	$html = '<div>' . $tabla->create(true) . '</div>';
	return $html;
}

function meterItem(&$cell, $item) {
	$cell[0]->content = $item->idAlmacen;
	$cell[1]->content = $item->idArticulo . ' - ' . $item->articulo->nombre;
	$cell[2]->content = $item->idColorPorArticulo;
	$cell[3]->content = $item->cantidadTotal;
	$cell[4]->content = divTablita($item);
}

$idCliente = Funciones::get('idCliente');
$empresa = Funciones::session('empresa');

try {
	$html = '';
	$where = '';
	if ($idCliente)
		$where .= ' AND (cod_cliente = ' . Datos::objectToDB($idCliente) . ')';
	if ($empresa)
		$where .= ' AND (empresa = ' . Datos::objectToDB($empresa) . ')';
	$where = trim($where, ' AND ') . ($where ? ' AND ' : '');
	$where .= ' (anulado = ' . Datos::objectToDB('N') . ' OR anulado IS NULL) AND (cod_ecommerce_order IS NULL) AND (nro_factura IS NULL) ';
	$order = ' ORDER BY razon_social ASC, cod_cliente ASC, empresa ASC, cod_sucursal ASC, fecha_remito DESC';

	$remitos = Factory::getInstance()->getListObject('Remito', $where . $order);
	if (count($remitos) == 0)
		throw new FactoryExceptionCustomException('No hay remitos pendientes con ese filtro');

	foreach ($remitos as $remito) {
		//CREO TABLA NUEVA
		$tabla = new HtmlTable(array('cantRows' => count($remito->detalle), 'cantCols' => 5, 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%'));
		armoHead($tabla);
		$tabla->getRowCellArray($rows, $cells);
		for ($i = 0; $i < count($remito->detalle); $i++)
			meterItem($cells[$i], $remito->detalle[$i]);
		$nro = $remito->numero;

		$divPdf = Html::echoBotonera(array('boton' => 'pdf', 'class' => 'pRight5 inline-block', 'style' => 'vertical-align: top;', 'tamanio' => '25', 'accion' => 'pdfClickRemito(' . $remito->empresa . ', ' . $nro . ');'), true);
		$divNombre = '<div class="inline-block lh25">' . $remito->cliente->razonSocial . ' - ' . $remito->sucursal->nombre . ' - ' . $remito->empresa . ' - Pares: ' . $remito->cantidadPares . '</div>';
		$divImporte = '<div class="fRight pRight10 lh25">$ <span id="spanImporteRemito_' . $nro . '" class="spanImporteRemito">0,00</span><input type="checkbox" class="chkUno" id="chkUno_' . $nro . '" importe="' . Funciones::formatearDecimales($remito->importe, 2) . '" /></div>';
		$echo = '<div id="divRemito_' . $nro . '" idRem="' . $nro . '">
		<div>' . $divPdf . $divNombre . $divImporte . '</div>
		<div>' . $tabla->create(true) . '</div>
		</div>';
		$html .= $echo;
	}

	echo $html;
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}
?>
<?php } ?>