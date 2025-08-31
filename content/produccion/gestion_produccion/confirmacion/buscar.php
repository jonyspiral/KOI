<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/gestion_produccion/confirmacion/buscar/')) { ?>
<?php

function divTablita($item, $esPdf){
	/** @var TareaProduccionItem $item */
	$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 8, 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->getHeadArray($ths);
	$tabla->getRowCellArray($rows, $cells);

	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$talle = $item->articulo->rangoTalle->posicion[$i + 1];
		$ths[$i]->content = ($talle ? $talle : '-');
		$ths[$i]->class =  ($i % 2 ? 'w12p' : 'w13p');

		$cant = ($talle ? $item->pendiente[$i + 1] : 0);
		$cells[0][$i]->content = ($esPdf ? '<label>' . $cant . '</label>' : '<input class="tarea_' . $item->idOrdenDeFabricacion . '-' . $item->numeroTarea . ' textbox w25 aCenter inputPar" type="text" validate="Entero" value="' . $cant . '" ');
		$cells[0][$i]->content .= ($esPdf ? '' : 'data-idordendefabricacion="' . $item->idOrdenDeFabricacion . '" data-numerotarea="' . $item->numeroTarea . '" data-maxcant="' . $cant . '" />');
		$cells[0][$i]->class = 'aCenter';
	}
	$tabla->headerClass('tableHeader');

	return $tabla->create(true);
}

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColorArticulo = Funciones::get('idColorArticulo');
$numeroOrdenFabricacion = Funciones::get('numeroOrdenFabricacion');
$numeroTarea = Funciones::get('numeroTarea');
$one = Funciones::get('one') == '1';
$orden = Funciones::get('orden');
$esPdf = Funciones::get('pdf') == '1';

try {
	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'pendiente > 0 AND ';
	$where .= 'vigente = ' . Datos::objectToDB('S') . ' AND ';
	$where .= 'articulo_vigente = ' . Datos::objectToDB('S') . ' AND ';
	$where .= 'naturaleza = ' . Datos::objectToDB('PT') . ' AND ';
	$where .= 'cod_seccion = ' . Datos::objectToDB(60) . ' AND ';
	$where .= 'cumplido_paso = ' . Datos::objectToDB('S');
	if (!$one) {
		($fechaDesde || $fechaHasta) && $where .= ' AND ' . Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_salida_real');
		($idAlmacen) && $where .= ' AND (cod_almacen = ' . Datos::objectToDB($idAlmacen) . ')';
		($idArticulo) && $where .= ' AND (cod_articulo = ' . Datos::objectToDB($idArticulo) . ')';
		($idColorArticulo) && $where .= ' AND (cod_color_articulo = ' . Datos::objectToDB($idColorArticulo) . ')';
		($numeroOrdenFabricacion) && $where .= ' AND (nro_orden_fabricacion LIKE ' . Datos::objectToDB('%' . $numeroOrdenFabricacion . '%') . ')';
	} else {
		($numeroOrdenFabricacion) && $where .= ' AND (nro_orden_fabricacion = ' . Datos::objectToDB($numeroOrdenFabricacion) . ')';
		($numeroTarea) && $where .= ' AND (nro_tarea = ' . Datos::objectToDB($numeroTarea) . ')';
	}
	$where = trim($where, ' AND ');
	$order = ' ORDER BY ';
	switch ($orden) {
		case 1: $order .= 'fecha_salida_real ASC, nro_orden_fabricacion ASC, nro_tarea ASC'; break;
		case 2: $order .= 'nro_orden_fabricacion DESC, nro_tarea DESC'; break;
		case 3: $order .= 'nro_orden_fabricacion ASC, nro_tarea ASC'; break;
		default: $order .= 'fecha_salida_real DESC, nro_orden_fabricacion DESC, nro_tarea DESC'; break;
	}

	$items = Factory::getInstance()->getListObject('TareaProduccionItem', $where . $order);

	if (!$one) {
		if (!count($items)) {
			throw new FactoryExceptionCustomException('No hay tareas cumplidas pendientes de confirmar con ese filtro');
		}

		$arrayHeader = array(
			array('content' => 'F. cumpl.', 'dataType' => 'Center', 'width' => 10, 'title' => 'Fecha de cumplido'),
			array('content' => 'Tarea', 'dataType' => 'Center', 'width' => 8),
			array('content' => 'Artículo', 'width' => 27),
			array('content' => 'Total', 'dataType' => 'Center', 'width' => 8),
			array('content' => 'Cantidad pendiente', 'dataType' => 'Center', 'width' => 42),
		);

		if(!$esPdf) {
			$arrayHeader[] = array('content' => '', 'dataType' => 'Center', 'width' => 5);
		}

		$tabla = new HtmlTable(array('cantRows' => count($items), 'cantCols' => 5 + ($esPdf ? 0 : 1), 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%'));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray($arrayHeader);
		$i = 0;
		foreach ($items as $item) {
			/** @var TareaProduccionItem $item */
			$cells[$i][0]->content = $item->fechaSalidaReal;
			$cells[$i][1]->content = $item->idOrdenDeFabricacion . ' - ' . $item->numeroTarea;
			$cells[$i][2]->content = '[' . $item->idAlmacen . '-' . $item->idArticulo . '-' . $item->idColorPorArticulo . '] ' . $item->articulo->nombre . ' - ' . $item->colorPorArticulo->nombre;
			$cells[$i][3]->content = $item->pendienteTotal;
			$cells[$i][3]->id = 'total_' . $item->idOrdenDeFabricacion . '-' . $item->numeroTarea;
			$cells[$i][4]->content = divTablita($item, $esPdf);

			if(!$esPdf) {
				$cells[$i][5]->content = '<a href="#" class="boton btnConfirmar" title="Confirmar" style="display: inline;" ';
				$cells[$i][5]->content .= 'data-idordendefabricacion="' . $item->idOrdenDeFabricacion . '" data-numerotarea="' . $item->numeroTarea . '"><img src="/img/botones/25/aceptar.gif"></a>';
			}

			$rows[$i]->id = 'row_' . $item->idOrdenDeFabricacion . '-' . $item->numeroTarea;
			$i++;
		}

		$html = $tabla->create(true);
		echo $html;
	} else {
		if (count($items) > 1) {
			throw new FactoryExceptionCustomException('No se pudo actualizar correctamente el registro ya que devolvió múltiples filas');
		}
		Html::jsonEncode('', array('idOrdenDeFabricacion' => $numeroOrdenFabricacion, 'numeroTarea' => $numeroTarea, 'cantidad' => count($items), 'pendiente' => count($items) ? $items[0]->pendiente : array()));
	}
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>