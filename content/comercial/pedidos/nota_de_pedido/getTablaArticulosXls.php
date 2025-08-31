<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/nota_de_pedido/buscar/')) { ?>
<?php

function f($num) {
	return Funciones::formatearDecimales($num, 2);
}
function p($num) {
	return ($num > 0 ? $num : 0);
}

function armoHead(&$tabla, $item) {
	$row = new HtmlTableRow();
	for ($j = 0; $j < $tabla->cantCols; $j++)
		$cells[$j] = new HtmlTableCell();

	$cells[0]->content = 'Cod.';
	$cells[0]->dataType = 'Center';
	$cells[1]->content = 'Artículo';
	$cells[2]->content = 'Color';
	$cells[2]->dataType = 'Center';
	$cells[3]->content = 'Pcio. Púb.';
	$cells[3]->dataType = 'DosDecimales';
	$cells[4]->content = 'Pcio. May.';
	$cells[4]->dataType = 'DosDecimales';
	$cells[5]->content = 'Curva';
	$cells[6]->content = 'Rango';
	$cells[6]->dataType = 'Center';
	$cells[7]->content = $item->articulo->rangoTalle->posicion[1];
	$cells[9]->content = $item->articulo->rangoTalle->posicion[2];
	$cells[11]->content = $item->articulo->rangoTalle->posicion[3];
	$cells[13]->content = $item->articulo->rangoTalle->posicion[4];
	$cells[15]->content = $item->articulo->rangoTalle->posicion[5];
	$cells[17]->content = $item->articulo->rangoTalle->posicion[6];
	$cells[19]->content = $item->articulo->rangoTalle->posicion[7];
	$cells[21]->content = $item->articulo->rangoTalle->posicion[8];
	$cells[23]->content = 'Total';
	$cells[24]->content = 'Importe';

	for ($j = 0; $j < $tabla->cantCols; $j++)
		$row->addCell($cells[$j]);
	$row->class .= ' bold';
	$tabla->addRow($row);
	return $tabla;
}

function armarTablaCategoria(&$html, &$arrayCurvas, $categoria, $stock, $tipoPrecio) {
	$articulos = Factory::getInstance()->getListObject('ColorPorArticulo', 'categoria_usuario = ' . Datos::objectToDB($categoria->id) . ' AND vigente = \'S\' AND naturaleza = \'PT\' ORDER BY denom_articulo ASC, cod_articulo ASC, cod_color_articulo ASC');

	$tabla = new HtmlTable(array('cantCols' => 25, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tabla->caption = $categoria->nombre;

	$i = 0;
	$lastRango = 0;
	foreach($articulos as $item){
        /** @var ColorPorArticulo $item */
		$arrStock = $stock[$item->articulo->id][$item->id];
		if ($lastRango != $item->articulo->rangoTalle->id) {
			armoHead($tabla, $item);
			$lastRango = $item->articulo->rangoTalle->id;
		}
		if ($item->formaDeComercializacion == 'M')
			foreach($item->curvas as $curva)
				if (!isset($arrayCurvas[$categoria->id]) || !isset($arrayCurvas[$categoria->id][$lastRango]) || !isset($arrayCurvas[$categoria->id][$lastRango][$curva->idCurva]))
					$arrayCurvas[$categoria->id][$lastRango][$curva->idCurva] = $curva;
			
		$row = new HtmlTableRow();
		for ($j = 0; $j < $tabla->cantCols; $j++)
			$cells[$j] = new HtmlTableCell();

		$cells[0]->content = $item->articulo->id;
		$cells[1]->content = $item->articulo->nombre;
		$cells[2]->content = $item->id;
		$cells[3]->content = f($item->precioMinoristaDolar);
		if ($tipoPrecio == 'D')
			$cells[4]->content = f($item->precioDistribuidor);
		else
			$cells[4]->content = f($item->precioMayoristaDolar);
		$cells[5]->content = $item->formaDeComercializacionNombreCorto;
		if ($item->formaDeComercializacion == 'T')
			$cells[5]->class .= ' bLightGreen';
		$cells[6]->content = $item->articulo->rangoTalle->posicionInicial . '-' . $item->articulo->rangoTalle->posicionFinal;
		if ($item->formaDeComercializacion == 'A') {
			$cells[7]->colspan = 18;
			$cells[7]->content = 'AGOTADO - AGOTADO - AGOTADO - AGOTADO - AGOTADO - AGOTADO - AGOTADO';
			$cells[7]->class .= ' aCenter bLightRed';
		} else {
			$cells[7]->content = p($arrStock[1]);
			$cells[7]->class .= ' white bBlack';
		}
		$cells[8]->content = '';
		$cells[9]->content = p($arrStock[2]);
		$cells[9]->class .= ' white bBlack';
		$cells[10]->content = '';
		$cells[11]->content = p($arrStock[3]);
		$cells[11]->class .= ' white bBlack';
		$cells[12]->content = '';
		$cells[13]->content = p($arrStock[4]);
		$cells[13]->class .= ' white bBlack';
		$cells[14]->content = '';
		$cells[15]->content = p($arrStock[5]);
		$cells[15]->class .= ' white bBlack';
		$cells[16]->content = '';
		$cells[17]->content = p($arrStock[6]);
		$cells[17]->class .= ' white bBlack';
		$cells[18]->content = '';
		$cells[19]->content = p($arrStock[7]);
		$cells[19]->class .= ' white bBlack';
		$cells[20]->content = '';
		$cells[21]->content = p($arrStock[8]);
		$cells[21]->class .= ' white bBlack';
		$cells[22]->content = '';
		$cells[23]->content = '=(I###FILA### + K###FILA### + M###FILA### + O###FILA### + Q###FILA### + S###FILA### + U###FILA### + W###FILA###)';
		$cells[24]->content = '=X###FILA###*E###FILA###';

		for ($j = 0; $j < $tabla->cantCols; $j++)
			$row->addCell($cells[$j]);
		if (!($i % 2))
			$row->class .= ' bDarkGray';
		$tabla->addRow($row);
		$i++;
	}

	$htmlTabla = $tabla->create(true);
	$html .= $htmlTabla;
	return $htmlTabla;
}

function armoTrRangos(&$tabla, $idRango) {
	$rango = Factory::getInstance()->getRangoTalle($idRango);
	$row = new HtmlTableRow();
	for ($j = 1; $j <= $tabla->cantCols; $j++) {
		$cells[$j] = new HtmlTableCell();
		if ($j > 7 && $j < 16) {
			$cells[$j]->content = $rango->posicion[$j - 7];
		} elseif ($j == 16)
			$cells[$j]->content = 'Total';
		else
			$cells[$j]->content = '';
		$row->addCell($cells[$j]);
	}
	$row->class .= ' bold';
	$tabla->addRow($row);
}

function armarTablaCurvas(&$html, $idCategoria, $rangos) {
	$tabla = new HtmlTable(array('cantCols' => 25, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$categoria = Factory::getInstance()->getCategoriaCalzadoUsuario($idCategoria);
	$tabla->caption = 'Curvas para ' . $categoria->nombre;
	foreach($rangos as $idRango => $curvas) {
		armoTrRangos($tabla, $idRango);
		foreach($curvas as $curva) {
			$row = new HtmlTableRow();
			$tempTotal = 0;
			for ($j = 1; $j <= $tabla->cantCols; $j++) {
				$cells[$j] = new HtmlTableCell();
				if ($j > 7 && $j < 16) {
					$tempTotal += $curva->cantidad[$j - 7];
					$cells[$j]->content = $curva->cantidad[$j - 7];
				} elseif ($j == 16)
					$cells[$j]->content = $tempTotal;
				else
					$cells[$j]->content = '';
				$row->addCell($cells[$j]);
			}
			$tabla->addRow($row);
		}
	}

	$htmlTabla = $tabla->create(true);
	$html .= $htmlTabla;
	return $htmlTabla;
}

$tipoPrecio = Funciones::get('tipoPrecio');

try {
	$html = '';
	$arrayCurvas = array();
	$stock = Stock::getStockMenosPendiente('01');
	$categorias = Factory::getInstance()->getListObject('CategoriaCalzadoUsuario', '1 = 1 ORDER BY orden ASC');
	foreach($categorias as $categoria)
		armarTablaCategoria($html, $arrayCurvas, $categoria, $stock, $tipoPrecio);
	foreach($arrayCurvas as $idCategoria => $rangos)
		armarTablaCurvas($html, $idCategoria, $rangos);

	echo $html;
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}




?>
<?php } ?>