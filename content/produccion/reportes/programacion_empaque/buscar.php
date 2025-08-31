<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/reportes/programacion_empaque/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$fechaDesdeEmpaque = Funciones::get('fechaDesdeEmpaque');
$fechaHastaEmpaque = Funciones::get('fechaHastaEmpaque');
$anulado = Funciones::get('anulado');
$cumplidoPaso = Funciones::get('cumplidoPaso');
$tipoTarea = Funciones::get('tipoTarea');
$situacion = Funciones::get('situacion');
$articulo = Funciones::get('articulo');
$lote = Funciones::get('lote');
$tarea = Funciones::get('tarea');
$orderBy = Funciones::get('orderBy');

try {
	//Order by
	$arrayOrderBy = array(
		0 => 'fecha_inicio DESC',
		1 => 'cod_articulo',
		2 => 'nro_tarea'
	);

	//Armo el where
	$strFechaInicio = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_inicio');
	$strFechaInicioEmpaque = Funciones::strFechas($fechaDesdeEmpaque, $fechaHastaEmpaque, 'fecha_programacion');

	$where = (empty($strFechaInicio) ? '' : $strFechaInicio . ' AND ');
	$where .= (empty($strFechaInicioEmpaque) ? '' : $strFechaInicioEmpaque . ' AND ');
	$where .= (empty($articulo) ? '' : 'cod_articulo = ' . Datos::objectToDB($articulo) . ' AND ');
	$where .= (empty($lote) ? '' : 'nro_plan = ' . Datos::objectToDB($lote) . ' AND ');
	$where .= (empty($tarea) ? '' : 'nro_tarea = ' . Datos::objectToDB($tarea) . ' AND ');
	$where .= 'anulado = ' . Datos::objectToDB($anulado) . ' AND ';
	$where .= (empty($cumplidoPaso) ? '' : 'cumplido_paso = ' . Datos::objectToDB($cumplidoPaso) . ' AND ');
	$where .= (empty($tipoTarea) ? '' : 'tipo_tarea ' . ($tipoTarea == 'D' ? '= ' : '!=') . Datos::objectToDB('D'));
	$where .= (empty($situacion) ? '' : 'situacion = ' . Datos::objectToDB($situacion) . ' AND ');
	$where = trim($where, ' AND ');
	$orderBy = (empty($arrayOrderBy[$orderBy]) ? '' : ' ORDER BY ' . $arrayOrderBy[$orderBy]);

	$lista = Factory::getInstance()->getArrayFromView('programacion_empaque_v', $where . $orderBy);
	if(empty($lista)) {
		throw new FactoryExceptionCustomException('No existen tareas con el filtro especificado');
	}

	$tabla = new HtmlTable(array('cantRows' => count($lista), 'cantCols' => 19, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								 'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray'));

	$tabla->createHeaderFromArray(
		  array(
			   array('content' => 'F. inicio', 'dataType' => 'Fecha', 'title' => 'Fecha inicio', 'width' => 6),
			   array('content' => 'L - O - T', 'dataType' => 'Center', 'title' => 'Lote - Orden - Tarea', 'width' => 7),
			   array('content' => 'Artículo', 'width' => 21),
			   array('content' => 'Color', 'dataType' => 'Center', 'width' => 5),
			   array('content' => 'F. corte', 'dataType' => 'Fecha', 'title' => 'Fecha corte', 'width' => 6),
			   array('content' => 'F. aparado', 'dataType' => 'Fecha', 'title' => 'Fecha aparado', 'width' => 6),
			   array('content' => 'F. armado', 'dataType' => 'Fecha', 'title' => 'Fecha armado', 'width' => 6),
			   array('content' => 'F. prog.', 'dataType' => 'Fecha', 'title' => 'Fecha programación', 'width' => 6),
			   array('content' => 'Cant.', 'dataType' => 'Center', 'title' => 'Cantidad', 'width' => 4),
			   array('content' => 'Pos.', 'dataType' => 'Center', 'title' => 'Posición inicial', 'width' => 4),
			   array('content' => 'C1', 'dataType' => 'Center', 'width' => 3),
			   array('content' => 'C2', 'dataType' => 'Center', 'width' => 3),
			   array('content' => 'C3', 'dataType' => 'Center', 'width' => 3),
			   array('content' => 'C4', 'dataType' => 'Center', 'width' => 3),
			   array('content' => 'C5', 'dataType' => 'Center', 'width' => 3),
			   array('content' => 'C6', 'dataType' => 'Center', 'width' => 3),
			   array('content' => 'C7', 'dataType' => 'Center', 'width' => 3),
			   array('content' => 'C8', 'dataType' => 'Center', 'width' => 3),
			   array('content' => 'U.P.C.', 'dataType' => 'Center', 'title' => 'Ultimo paso cumplido', 'width' => 5)
		  )
	);
	$tabla->getRowCellArray($rows, $cells);

	for ($i = 0; $i < count($lista); $i++) {
		$registro = $lista[$i];
		$registro['pares'] = ($registro['tipo_documento'] == 'NDB' ? 0 : $registro['pares']);

		$cells[$i][0]->content = Funciones::formatearFecha($registro['fecha_inicio'], 'd/m/Y');
		$cells[$i][1]->content = $registro['nro_plan'] . ' - ' . $registro['nro_orden_fabricacion'] . ' - ' . $registro['nro_tarea'];
		$cells[$i][2]->content = '[' . $registro['cod_articulo'] . '] ' . $registro['denom_articulo'];
		$cells[$i][3]->content = $registro['cod_color_articulo'];
		$cells[$i][4]->content = Funciones::formatearFecha($registro['fecha_corte'], 'd/m/Y');
		$cells[$i][5]->content = Funciones::formatearFecha($registro['fecha_aparado'], 'd/m/Y');
		$cells[$i][6]->content = Funciones::formatearFecha($registro['fecha_armado'], 'd/m/Y');
		$cells[$i][7]->content = Funciones::formatearFecha($registro['fecha_programacion'], 'd/m/Y');
		$cells[$i][8]->content = $registro['cantidad'];
		$cells[$i][9]->content = $registro['posic_1'];
		$cells[$i][10]->content = Funciones::toInt($registro['pos_1_cant']);
		$cells[$i][11]->content = Funciones::toInt($registro['pos_2_cant']);
		$cells[$i][12]->content = Funciones::toInt($registro['pos_3_cant']);
		$cells[$i][13]->content = Funciones::toInt($registro['pos_4_cant']);
		$cells[$i][14]->content = Funciones::toInt($registro['pos_5_cant']);
		$cells[$i][15]->content = Funciones::toInt($registro['pos_6_cant']);
		$cells[$i][16]->content = Funciones::toInt($registro['pos_7_cant']);
		$cells[$i][17]->content = Funciones::toInt($registro['pos_8_cant']);
		$cells[$i][18]->content = $registro['ultimo_paso_cumplido'];

		if($registro['tipo_tarea'] == 'D'){
			for ($j = 0; $j < $tabla->cantCols; $j++) {
				$cells[$i][$j]->class .= ' bold';
			}
		}

		$cells[$i][6]->class .= ' bLightGray';
		for ($j = 9; $j < 17; $j++) {
			$cells[$i][$j]->class .= ' bLightGray';
		}
	}

	$tabla->create();

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
