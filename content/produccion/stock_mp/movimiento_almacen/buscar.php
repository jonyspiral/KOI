<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock_mp/movimiento_almacen/buscar/')) { ?>
<?php

/**
 * @param $id
 *
 * @return RangoTalle
 */
function getRango($id) {
	global $rangos;
    if (!$id) {
        return null;
    }
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
	$combinado = $item['cod_almacen'] . '-' . $item['cod_material'] . '-' . $item['cod_color'];

	$rango = getRango($item['cod_rango']);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$talle = $rango ? $rango->posicion[$i + 1] : ($i == 0 ? 'U' : '');
		$ths[$i]->content = ($talle ? $talle : '-');
		$ths[$i]->class .=  ($i % 2 ? ' w12p' : ' w13p');

		$cant = ($talle ? $item['S' . ($i + 1)] : 0);
		$cells[0][$i]->content = '<label class="stock_' . $combinado . '">' . $cant . '</label>';
		$cells[0][$i]->class = 'aCenter';

		$cells[1][$i]->content = '<input class="material_' . $combinado . ' textbox w25 aCenter inputPar" type="text" validate="Entero" value="0" ';
		$cells[1][$i]->content .= 'data-idalmacen="' . $item['cod_almacen'] . '" data-idmaterial="' . $item['cod_material'] . '" data-idcolormateriaprima="' . $item['cod_color'] . '" data-maxcant="' . $cant . '" />';
		$cells[1][$i]->class = 'aCenter';
	}

	return $tabla->create(true);
}

$idAlmacen = Funciones::get('idAlmacen');
$idMaterial = Funciones::get('idMaterial');
$idColor = Funciones::get('idColor');
$one = Funciones::get('one') == '1';
$orden = Funciones::get('orden');
$rangos = array();

try {
	if (!$idAlmacen) {
		throw new FactoryExceptionCustomException('Debe elegir el almacén desde el cual quiere realizar los movimientos');
	}

	$where = 'cod_almacen = ' . Datos::objectToDB($idAlmacen);
	(!$one) && $where .= ' AND (cant_s > 0)';
	($idMaterial) && $where .= ' AND (cod_material = ' . Datos::objectToDB($idMaterial) . ')';
	($idColor) && $where .= ' AND (cod_color = ' . Datos::objectToDB($idColor) . ')';
	$where = trim($where, ' AND ');
	$order = ' ORDER BY ';
	switch ($orden) {
		case 1: $order .= 'cod_material DESC, cod_color DESC'; break;
		case 2: $order .= 'cant_s DESC, cod_material ASC, cod_color ASC'; break;
		case 3: $order .= 'cant_s ASC, cod_material ASC, cod_color ASC'; break;
		default: $order .= 'cod_material ASC, cod_color ASC'; break;
	}

	$items = Factory::getInstance()->getArrayFromView('stock_mp_vw', $where . $order);

	if (!$one) {
		if (!count($items)) {
			throw new FactoryExceptionCustomException('No hay materiales que cumplan con ese filtro');
		}

		$tabla = new HtmlTable(array('cantRows' => count($items), 'cantCols' => 7, 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%'));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray(
			  array(
				   array('content' => 'Alm. actual', 'dataType' => 'Center', 'width' => 10, 'title' => 'Almacén actual'),
				   array('content' => 'Material', 'width' => 18),
				   array('content' => 'Stock', 'dataType' => 'Center', 'width' => 35),
				   array('content' => 'Total', 'dataType' => 'Center', 'width' => 5),
				   array('content' => 'Mover a...', 'dataType' => 'Center', 'width' => 14),
				   array('content' => 'Motivo', 'dataType' => 'Center', 'width' => 13),
				   array('content' => '', 'dataType' => 'Center', 'width' => 5)
			  )
		);
		$i = 0;
		foreach ($items as $item) {
			$combinado = $item['cod_almacen'] . '-' . $item['cod_material'] . '-' . $item['cod_color'];
			$cells[$i][0]->content = '[' . $item['cod_almacen'] . '] ' . $item['nombre_almacen'];
			$cells[$i][1]->content = '[' . $item['cod_material'] . '-' . $item['cod_color'] . '] ' . $item['nombre_material'] . ' - ' . $item['nombre_color'];
			$cells[$i][2]->content = divTablita($item);
			$cells[$i][3]->content = 0;
			$cells[$i][3]->id = 'total_' . $combinado;
			$cells[$i][4]->content = '<input class="textbox obligatorio autoSuggestBox w140 mover_a" id="mover_a_' . $combinado . '" name="Almacen" />';
			$cells[$i][5]->content = '<textarea class="textbox obligatorio noResize w140 motivo" id="motivo_' . $combinado . '" rows="3" style="height: auto;"></textarea>';
			$cells[$i][6]->content = '<a href="#" class="boton btnConfirmar" title="Confirmar" style="display: inline;" ';
			$cells[$i][6]->content .= 'data-idalmacen="' . $item['cod_almacen'] . '" data-idmaterial="' . $item['cod_material'] . '" data-idcolormateriaprima="' . $item['cod_color'] . '"><img src="/img/botones/25/aceptar.gif"></a>';

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
		Html::jsonEncode('', array('idAlmacen' => $item['cod_almacen'], 'idMaterial' => $item['cod_material'], 'idColor' => $item['cod_color'], 'cantidadTotal' => $item['cant_s'], 'cantidad' => $cantidades));
	}
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>