<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock/movimientos/buscar/')) { ?>
<?php

$CANT_MAX_REGISTROS = 300;

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$tipoMovimiento = Funciones::get('tipoMovimiento');
$tipoOperacion = Funciones::get('tipoOperacion');
$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColorArticulo = Funciones::get('idColorArticulo');
$orden = Funciones::get('orden');
$confirmar = (Funciones::get('confirmar') == '1');

function getDetalleOperacion($tipo) {
	$ret = '-';
	switch ($tipo) {
		case TiposOperacionStock::ajusteStock: $ret = 'AJU'; break;
		case TiposOperacionStock::confirmacionStock: $ret = 'TAR'; break;
		case TiposOperacionStock::remito: $ret = 'REM'; break;
		case TiposOperacionStock::notaDeCredito: $ret = 'NCR'; break;
		case TiposOperacionStock::movimientoAlmacen: $ret = 'ALM'; break;
	}
	return $ret;
}

try {
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_alta');
	($tipoMovimiento != '0') && $where .= ' AND tipo_movimiento = ' . Datos::objectToDB($tipoMovimiento);
	($tipoOperacion != '0') && $where .= ' AND tipo_operacion = ' . Datos::objectToDB($tipoOperacion);
	($idAlmacen) && $where .= ' AND cod_almacen = ' . Datos::objectToDB($idAlmacen);
	($idArticulo) && $where .= ' AND cod_articulo = ' . Datos::objectToDB($idArticulo);
	($idColorArticulo) && $where .= ' AND cod_color_articulo = ' . Datos::objectToDB($idColorArticulo);
	if (!($where = trim($where, ' AND '))) {
		$where = ' 1 = 1 ';
	}
	$order = ' ORDER BY ';
	switch ($orden) {
		case 1: $order .= 'fecha_alta ASC, id ASC'; break;
		case 2: $order .= 'cod_almacen ASC, cod_articulo ASC, cod_color_articulo ASC, id ASC'; break;
		case 3: $order .= 'cod_almacen DESC, cod_articulo DESC, cod_color_articulo DESC, id DESC'; break;
		case 4: $order .= 'tipo_operacion ASC, id ASC'; break;
		default: $order .= 'fecha_alta DESC, id DESC'; break;
	}
	$listaMovimientos = Factory::getInstance()->getArrayFromView('movimientos_stock_v', $where . $order, $CANT_MAX_REGISTROS);
	$fields = ' SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cantidad) cantidad,
				SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cant_1) cant_1,
				SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cant_2) cant_2,
				SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cant_3) cant_3,
				SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cant_4) cant_4,
				SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cant_5) cant_5,
				SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cant_6) cant_6,
				SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cant_7) cant_7,
				SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cant_8) cant_8,
				SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cant_9) cant_9,
				SUM((CASE tipo_movimiento WHEN ' . Datos::objectToDB('NEG') . ' THEN -1 ELSE 1 END) * cant_10) cant_10';
	$totalMovimientos = Factory::getInstance()->getArrayFromView('movimientos_stock_v', $where, $CANT_MAX_REGISTROS, $fields);
	if (empty($listaMovimientos)) {
		throw new FactoryExceptionCustomException('No existen movimientos con el filtro especificado');
	}
	if (count($listaMovimientos) >= $CANT_MAX_REGISTROS) {
		if (!$confirmar) {
			Html::jsonConfirm('La consulta devolvió demasiados registros. Se mostrarán sólo ' . $CANT_MAX_REGISTROS . '. ¿Desea continuar?', 'confirmar');
			exit;
		}
	}

	$tabla = new HtmlTable(array('cantRows' => count($listaMovimientos), 'cantCols' => 14, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								'tdBaseClass' => 'bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'bBottomDarkGray bLeftDarkGray bRightDarkGray'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'ID', 'dataType' => 'Center', 'width' => 5),
			 array('content' => 'Fecha', 'dataType' => 'Fecha', 'width' => 8),
			 array('content' => 'Tipo', 'dataType' => 'Center', 'width' => 4),
			 array('content' => 'Operación', 'width' => 20),
			 array('content' => 'Artículo', 'width' => 26),
			 array('content' => 'P1', 'dataType' => 'Entero', 'width' => 4),
			 array('content' => 'P2', 'dataType' => 'Entero', 'width' => 4),
			 array('content' => 'P3', 'dataType' => 'Entero', 'width' => 4),
			 array('content' => 'P4', 'dataType' => 'Entero', 'width' => 4),
			 array('content' => 'P5', 'dataType' => 'Entero', 'width' => 4),
			 array('content' => 'P6', 'dataType' => 'Entero', 'width' => 4),
			 array('content' => 'P7', 'dataType' => 'Entero', 'width' => 4),
			 array('content' => 'P8', 'dataType' => 'Entero', 'width' => 4),
			 array('content' => 'Total', 'dataType' => 'Entero', 'width' => 5)
		)
	);

	for ($i = 0; $i < count($listaMovimientos); $i++) {
		$item = $listaMovimientos[$i];

		$rows[$i]->class = $item['tipo_movimiento'] == TiposMovimientoStock::positivo ? 'indicador-verde' : ($item['tipo_movimiento'] == TiposMovimientoStock::negativo ? 'indicador-rojo' : 'indicador-gris');

		$cells[$i][0]->content = $item['id'];
		$cells[$i][1]->content = Funciones::formatearFecha($item['fecha_alta'], 'd/m/Y');
		$cells[$i][2]->content = $item['tipo_movimiento'];
		$cells[$i][3]->content = $item['observaciones'];
		$cells[$i][3]->class = 'pLeft10';
		$cells[$i][4]->content = '[' . $item['cod_almacen'] . '-' . $item['cod_articulo'] . '-' . $item['cod_color_articulo'] . '] ' . $item['nombre_articulo'];
		$cells[$i][4]->class = 'pLeft10';
		for ($k = 1; $k <= 8; $k++) {
			$cells[$i][$k + 4]->content = $item['cant_' . $k];
			$cells[$i][$k + 4]->class = 'pRight10';
		}
		$cells[$i][13]->content = $item['cantidad'];
		$cells[$i][13]->class = 'bold pRight10';
	}

	//Agrego la de total movimientos
	$tabla->getFootArray($foots);
	$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
	$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
	$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';
	$item = $totalMovimientos[0];

	$foots[0]->content = 'TOTAL';
	$foots[0]->class .= ' aCenter';
	$foots[0]->colspan = 5;
	$foots[5]->content = $item['cant_1'];
	$foots[6]->content = $item['cant_2'];
	$foots[7]->content = $item['cant_3'];
	$foots[8]->content = $item['cant_4'];
	$foots[9]->content = $item['cant_5'];
	$foots[10]->content = $item['cant_6'];
	$foots[11]->content = $item['cant_7'];
	$foots[12]->content = $item['cant_8'];
	$tot = 0;
	for ($i = 1; $i <= 8; $i++) {
		$tot += Funciones::toNatural($item['cant_' . $i]);
	}
	$foots[13]->content = $tot;

	$tabla->create();

} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>