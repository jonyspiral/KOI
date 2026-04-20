<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/reportes/costos_articulos/buscar/')) { ?>
<?php

$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');
$tipoReporte = Funciones::get('tipoReporte');

function buscar($nombreVista, $where) {
	$listaRegistros = Factory::getInstance()->getArrayFromView($nombreVista, $where);
	if(empty($listaRegistros))
		throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado.');

	return $listaRegistros;
}

function armoHeadTablaDetallada(&$tabla) {
	$tabla->getHeadArray($ths);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		if ($i == 0) $ths[$i]->class = 'cornerL5';
		elseif ($i == $tabla->cantCols - 1) $ths[$i]->class = 'cornerR5 bLeftWhite';
		else $ths[$i]->class = 'bLeftWhite';
	}
	$tabla->headerClass('tableHeader');
	$ths[0]->content = 'Artículo';
	$ths[1]->content = 'C. Art';
	$ths[2]->content = 'Material';
	$ths[3]->content = 'C. Mat';
	$ths[4]->content = 'Conjunto';
	$ths[5]->content = 'Costo';
	$ths[5]->dataType = 'Moneda';
	return $tabla;
}

function armoHeadTablaAgrupada(&$tabla) {
	$tabla->getHeadArray($ths);
	$widths = array(70, 15, 15);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$ths[$i]->style->width = $widths[$i] . '%';
		if ($i == 0) $ths[$i]->class = 'cornerL5';
		elseif ($i == $tabla->cantCols - 1) $ths[$i]->class = 'cornerR5 bLeftWhite';
		else $ths[$i]->class = 'bLeftWhite';
	}

	$tabla->headerClass('tableHeader');
	$ths[0]->content = 'Artículo';
	$ths[1]->content = 'Código Color';
	$ths[2]->content = 'Costo';
	$ths[2]->dataType = 'Moneda';
	return $tabla;
}

try {
	//Validaciones generales
	if(empty($idArticulo) && !empty($idColor))
		throw new FactoryExceptionCustomException('No puede realizar una consulta seleccionando sólo un color.');

	//Armo el where
	$where = '';
	$where .= (empty($idArticulo) ? '' : 'cod_articulo = ' . Datos::objectToDB($idArticulo));
	$where .= (empty($idColor) ? '' : ' AND cod_color_articulo = ' . Datos::objectToDB($idColor));

	if($tipoReporte == 'D') {
		if(empty($idArticulo) || empty($idColor)){
			throw new FactoryExceptionCustomException('Debe especificar un artículo y color de artículo.');
		}

		$listaRegistros = buscar('costo_mp_producto_detalle_v', $where);

		$tabla = new HtmlTable(array('cantRows' => count($listaRegistros), 'cantCols' => 6, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
		$tabla->getRowCellArray($rows, $cells);

		armoHeadTablaDetallada($tabla);

		for ($i = 0; $i < count($listaRegistros); $i++) {
			$fila = $listaRegistros[$i];

			for ($j = 0; $j < $tabla->cantCols; $j++) {
				if ($j == 0) $cells[$i][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
				else $cells[$i][$j]->class .= ' bBottomDarkGray';
				if ($j == 5) $cells[$i][$j]->class .= ' bRightDarkGray bBottomDarkGray';
			}

			$cells[$i][0]->content = '[' . $fila['cod_articulo'] . '] ' . $fila['denom_articulo'];
			$cells[$i][1]->content = $fila['cod_color_articulo'];
			$cells[$i][2]->content = '[' . $fila['cod_material'] . '] ' . $fila['Material'];
			$cells[$i][3]->content = $fila['cod_color_material'];
			$cells[$i][4]->content = $fila['denom_conjunto'];
			$cells[$i][5]->content = $fila['Costo'];
		}

		for ($j = 3; $j < $tabla->cantCols; $j++) {
			$cells[$i][$j]->class = 'bLightOrange w70 bold bBottomDarkGray bold';
		}

	}elseif ($tipoReporte == 'A') {
		$listaRegistros = buscar('costo_mp_producto_v', $where);

		$tabla = new HtmlTable(array('cantRows' => count($listaRegistros), 'cantCols' => 3, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
		$tabla->getRowCellArray($rows, $cells);

		armoHeadTablaAgrupada($tabla);

		for ($i = 0; $i < count($listaRegistros); $i++) {
			$fila = $listaRegistros[$i];

			for ($j = 0; $j < $tabla->cantCols; $j++) {
				if ($j == 0) $cells[$i][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
				else $cells[$i][$j]->class .= ' bBottomDarkGray';
				if ($j == 2) $cells[$i][$j]->class .= ' bRightDarkGray bBottomDarkGray';
			}

			$cells[$i][0]->content = '[' . $fila['cod_articulo'] . '] ' . $fila['denom_articulo'];
			$cells[$i][1]->content = $fila['cod_color_articulo'];
			$cells[$i][2]->content = $fila['Costo'];
		}
	}else {
		throw new FactoryExceptionCustomException('Tipo de reporte incorrecto.');
	}

	$tabla->create();

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
