<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock_mp/confirmacion_movimiento_almacen/buscar/')) { ?>
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
	/** @var MovimientoAlmacenConfirmacionMP $item */
	$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 8, 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->headerClass('');
	$tabla->getHeadArray($ths);
	$tabla->headClass('bDarkGray aCenter bold bRightWhite white');
	$tabla->getRowCellArray($rows, $cells);
	$rows[0]->class = 's13';

	$rango = getRango($item->material->idRango);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$talle = $rango ? $rango->posicion[$i + 1] : ($i == 0 ? 'U' : null);
		$ths[$i]->content = ($talle ? $talle : '-');
		$ths[$i]->class .=  ($i % 2 ? ' w12p' : ' w13p');

		$cells[0][$i]->content = '<label class="movimiento_' . $item->id . '">' . ($talle ? $item->cantidad[($i + 1)] : 0) . '</label>';
		$cells[0][$i]->class = 'aCenter';
	}

	return $tabla->create(true);
}

$uxas = Factory::getInstance()->getArrayFromView('usuarios_por_almacen', 'cod_usuario = ' . Datos::objectToDB(Usuario::logueado()->id));
$almacenes = array();
foreach ($uxas as $uxa) {
	$almacenes[] = $uxa['cod_almacen'];
}

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$idAlmacen = Funciones::get('idAlmacen');
$idAlmacen = ($idAlmacen && in_array($idAlmacen, $almacenes)) ? array($idAlmacen) : $almacenes;
$idMaterial = Funciones::get('idMaterial');
$idColor = Funciones::get('idColor');
$mostrar = Funciones::get('mostrar'); //Puede ser 0) Pendientes, 1) Confirmados, 2) Rechazados, 3) Todos
$orden = Funciones::get('orden'); //Puede ser 0) "Por estado" (pendientes primero y ID ASC), 1) "Por fecha ascendente" (ID ASC), 2) "Por fecha descendente" (ID DESC)
$rangos = array();

try {
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_alta');
	(count($idAlmacen)) && $where .= ' AND (cod_almacen_origen IN (' . implode(',', $idAlmacen) . ') OR cod_almacen_destino IN (' . implode(',', $idAlmacen) . '))';
	($idMaterial) && $where .= ' AND cod_material = ' . Datos::objectToDB($idMaterial);
	($idColor) && $where .= ' AND cod_color = ' . Datos::objectToDB($idColor);
	if (!($where = trim($where, ' AND '))) {
		$where = ' 1 = 1 ';
	}
	switch ($mostrar) {
		case 1: $where .= ' AND confirmado = ' . Datos::objectToDB('S') . ' AND anulado = ' . Datos::objectToDB('N'); break;
		case 2: $where .= ' AND confirmado = ' . Datos::objectToDB('N') . ' AND anulado = ' . Datos::objectToDB('S'); break;
		case 3: $where .= ' '; break;
		default: $where .= ' AND confirmado = ' . Datos::objectToDB('N') . ' AND anulado = ' . Datos::objectToDB('N'); break;
	}
	$order = ' ORDER BY ';
	switch ($orden) {
		case 1: $order .= 'cod_confirmacion ASC'; break;
		case 2: $order .= 'cod_confirmacion DESC'; break;
		default: $order .= 'confirmado ASC, anulado ASC, cod_confirmacion ASC'; break;
	}

	$items = Factory::getInstance()->getListObject('MovimientoAlmacenConfirmacionMP', $where . $order);

	if (!count($items)) {
		throw new FactoryExceptionCustomException('No hay movimientos que cumplan con ese filtro');
	}

	$tabla = new HtmlTable(array('cantRows' => count($items), 'cantCols' => 7, 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		  array(
			   array('content' => 'Fecha', 'dataType' => 'Center', 'width' => 10, 'title' => 'Fecha'),
			   array('content' => 'Alm. ORI/DEST', 'width' => 10, 'title' => 'Almacén de origen y de destino'),
			   array('content' => 'Material', 'width' => 18),
			   array('content' => 'Movimiento', 'dataType' => 'Center', 'width' => 30),
			   array('content' => 'Total', 'dataType' => 'Center', 'width' => 5),
			   array('content' => 'Motivo', 'dataType' => 'Center', 'width' => 17),
			   array('content' => '', 'dataType' => 'Center', 'width' => 10)
		  )
	);
	$i = 0;
	foreach ($items as $item) {
		/** @var MovimientoAlmacenConfirmacionMP $item */
		$cells[$i][0]->content = $item->fechaAlta;
		$cells[$i][1]->content = '<span class="indicador-rojo w100p inline-block">' . $item->almacenOrigen->getIdNombre() . '</span><br><span class="indicador-verde w100p inline-block">' . $item->almacenDestino->getIdNombre() . '</span>';
		$cells[$i][2]->content = $item->colorMateriaPrima->getIdNombre('nombreColor', 'idColor');
		$cells[$i][3]->content = divTablita($item);
		$cells[$i][4]->content = $item->cantidadTotal;
		$cells[$i][4]->id = 'total_' . $item->id;
		$cells[$i][5]->content = $item->motivo;
		if ($item->pendiente() && in_array($item->almacenDestino->id, $almacenes)) {
			$cells[$i][6]->content = '<a href="#" class="boton btnConfirmar" title="Confirmar" data-idconfirmacion="' . $item->id . '" data-idalmacenorigen="' . $item->idAlmacenOrigen . '" data-idalmacendestino="' . $item->idAlmacenDestino . '" data-cantidadtotal="' . $item->cantidadTotal . '"><img src="/img/botones/25/aceptar.gif"></a>';
			$cells[$i][6]->content .= '<a href="#" class="boton btnRechazar" title="Rechazar" data-idconfirmacion="' . $item->id . '" data-idalmacenorigen="' . $item->idAlmacenOrigen . '" data-idalmacendestino="' . $item->idAlmacenDestino . '" data-cantidadtotal="' . $item->cantidadTotal . '"><img src="/img/botones/25/cancelar.gif"></a>';
		} elseif ($item->confirmado == 'S') {
			$cells[$i][6]->content = 'Movimiento confirmado por ' . $item->usuarioConfirmacion->nombreApellido . ' el ' . $item->fechaConfirmacion;
			$cells[$i][6]->class = 'indicador-verde';
		} elseif ($item->anulado == 'S') {
			$cells[$i][6]->content = 'Movimiento rechazado por ' . $item->usuarioBaja->nombreApellido . ' el ' . $item->fechaBaja;
			$cells[$i][6]->class = 'indicador-rojo';
		} else {
			$cells[$i][6]->content = 'Esperando confirmación o rechazo';
			$cells[$i][6]->class = 'indicador-gris';
		}

		$rows[$i]->id = 'row_' . $item->id;
		$i++;
	}

	$html = $tabla->create(true);
	echo $html;

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>