<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock/movimiento_almacen/buscar/')) { ?>
<?php

/**
 * @param $id
 *
 * @return RangoTalle
 */
function getRango($id) {
	global $rangos;
	if (!isset($rangos[$id])) {
		$rangos[$id] = Factory::getInstance()->getRangoTalle($id);
	}
	return $rangos[$id];
}

function divTablita($item){
	/** @var TareaProduccionItem $item */
	$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 8, 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->headerClass('');
	$tabla->getHeadArray($ths);
	$tabla->headClass('bDarkGray aCenter bold bRightWhite white');
	$tabla->getRowCellArray($rows, $cells);
	$rows[0]->class = 's13';
	$combinado = $item['cod_almacen'] . '-' . $item['cod_articulo'] . '-' . $item['cod_color_articulo'];

	$rango = getRango($item['cod_rango']);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$talle = $rango->posicion[$i + 1];
		$ths[$i]->content = ($talle ? $talle : '-');
		$ths[$i]->class .=  ($i % 2 ? ' w12p' : ' w13p');

		$cant = ($talle ? $item['S' . ($i + 1)] : 0);
		$cells[0][$i]->content = '<label class="stock_' . $combinado . '">' . $cant . '</label>';
		$cells[0][$i]->class = 'aCenter';

		$cells[1][$i]->content = '<input class="articulo_' . $combinado . ' textbox w25 aCenter inputPar" type="text" validate="Entero" value="0" ';
		$cells[1][$i]->content .= 'data-idalmacen="' . $item['cod_almacen'] . '" data-idarticulo="' . $item['cod_articulo'] . '" data-idcolorarticulo="' . $item['cod_color_articulo'] . '" data-maxcant="' . $cant . '" />';
		$cells[1][$i]->class = 'aCenter';
	}

	return $tabla->create(true);
}

$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColorArticulo = Funciones::get('idColorArticulo');
$one = Funciones::get('one') == '1';
$orden = Funciones::get('orden');
$rangos = array();

try {
	if (!$idAlmacen) {
		throw new FactoryExceptionCustomException('Debe elegir el almacén desde el cual quiere realizar los movimientos');
	}

	$where = 'cod_almacen = ' . Datos::objectToDB($idAlmacen);
	(!$one) && $where .= ' AND (cant_s > 0)';
	($idArticulo) && $where .= ' AND (cod_articulo = ' . Datos::objectToDB($idArticulo) . ')';
	($idColorArticulo) && $where .= ' AND (cod_color_articulo = ' . Datos::objectToDB($idColorArticulo) . ')';
	$where = trim($where, ' AND ');
	$order = ' ORDER BY ';
	switch ($orden) {
		case 1: $order .= 'cod_articulo DESC, cod_color_articulo DESC'; break;
		case 2: $order .= 'cant_s DESC, cod_articulo ASC, cod_color_articulo ASC'; break;
		case 3: $order .= 'cant_s ASC, cod_articulo ASC, cod_color_articulo ASC'; break;
		default: $order .= 'cod_articulo ASC, cod_color_articulo ASC'; break;
	}

	$items = Factory::getInstance()->getArrayFromView('stock_pt', $where . $order);

	if (!$one) {
		if (!count($items)) {
			throw new FactoryExceptionCustomException('No hay artículos que cumplan con ese filtro');
		}

		$tabla = new HtmlTable(array('cantRows' => count($items), 'cantCols' => 7, 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%'));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray(
			  array(
				   array('content' => 'Alm. actual', 'dataType' => 'Center', 'width' => 10, 'title' => 'Almacén actual'),
				   array('content' => 'Artículo', 'width' => 18),
				   array('content' => 'Stock', 'dataType' => 'Center', 'width' => 35),
				   array('content' => 'Total', 'dataType' => 'Center', 'width' => 5),
				   array('content' => 'Mover a...', 'dataType' => 'Center', 'width' => 14),
				   array('content' => 'Motivo', 'dataType' => 'Center', 'width' => 13),
				   array('content' => '', 'dataType' => 'Center', 'width' => 5)
			  )
		);
		$i = 0;
		foreach ($items as $item) {
			$combinado = $item['cod_almacen'] . '-' . $item['cod_articulo'] . '-' . $item['cod_color_articulo'];
			$cells[$i][0]->content = '[' . $item['cod_almacen'] . '] ' . $item['nombre_almacen'];
			$cells[$i][1]->content = '[' . $item['cod_articulo'] . '-' . $item['cod_color_articulo'] . '] ' . $item['nombre_articulo'] . ' - ' . $item['nombre_color'];
			$cells[$i][2]->content = divTablita($item);
			$cells[$i][3]->content = 0;
			$cells[$i][3]->id = 'total_' . $combinado;
			$cells[$i][4]->content = '<input class="textbox obligatorio autoSuggestBox w140 mover_a" id="mover_a_' . $combinado . '" name="Almacen" />';
			$cells[$i][5]->content = '<textarea class="textbox obligatorio noResize w140 motivo" id="motivo_' . $combinado . '" rows="3" style="height: auto;"></textarea>';
			$cells[$i][6]->content = '<a href="#" class="boton btnConfirmar" title="Confirmar" style="display: inline;" ';
			$cells[$i][6]->content .= 'data-idalmacen="' . $item['cod_almacen'] . '" data-idarticulo="' . $item['cod_articulo'] . '" data-idcolorarticulo="' . $item['cod_color_articulo'] . '"><img src="/img/botones/25/aceptar.gif"></a>';

			$rows[$i]->id = 'row_' . $combinado;
			$i++;
		}

		$html = $tabla->create(true);
		echo $html;
	} else {
		if (count($items) > 1) {
			throw new FactoryExceptionCustomException('No se pudo actualizar correctamente el registro ya que devolvió múltiples filas');
		}
		$item = $items[0];
		$cantidades = array();
		for ($i = 1; $i <= 10; $i++) {
			$cantidades[$i] = $item['S' . $i];
		}
		Html::jsonEncode('', array('idAlmacen' => $item['cod_almacen'], 'idArticulo' => $item['cod_articulo'], 'idColorArticulo' => $item['cod_color_articulo'], 'cantidadTotal' => $item['cant_s'], 'cantidad' => $cantidades));
	}
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>