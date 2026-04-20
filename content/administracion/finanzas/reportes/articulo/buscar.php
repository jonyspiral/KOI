<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/finanzas/reportes/articulo/buscar/')) { ?>
<?php

	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$empresa = Funciones::get('empresa');
	$articulo = Funciones::get('articulo');
	$color = Funciones::get('color');
	$cliente = Funciones::get('cliente');
	$orderBy = Funciones::get('orderBy');

	function armoHeadTabla(HtmlTable &$tabla) {
		$tabla->getHeadArray($ths);
		$widths = array(30, 30, 5, 5, 15, 15);
		for ($i = 0; $i < $tabla->cantCols; $i++) {
			$ths[$i]->style->width = $widths[$i] . '%';
			if ($i == 0) $ths[$i]->class = 'cornerL5';
			elseif ($i == $tabla->cantCols - 1) $ths[$i]->class = 'cornerR5 bLeftWhite';
			else $ths[$i]->class = 'bLeftWhite';
		}
		$tabla->headerClass('tableHeader');
		$ths[0]->content = 'Cliente';
		$ths[1]->content = 'Artículo';
		$ths[2]->content = 'Color';
		$ths[3]->content = 'Pares';
		$ths[3]->dataType = 'Entero';
		$ths[4]->content = 'Monto';
		$ths[4]->dataType = 'Moneda';
		$ths[5]->content = 'Promedio';
		$ths[5]->dataType = 'Moneda';
		return $tabla;
	}

	function comprobarFechas(&$desde, &$hasta) {
		$dias = 800;
		if (!isset($desde) && ! isset($hasta))
			throw new FactoryExceptionCustomException('Debe ingresar una fecha "desde" o una fecha "hasta"');

		if (!isset($desde)){
			$desde = Funciones::sumarTiempo($hasta, -1 * $dias, 'days');
			$_GET['fechaDesde'] = $hasta;
		}
		if (!isset($hasta)) {
			$hasta = Funciones::sumarTiempo($desde, $dias, 'days');
			$_GET['fechaHasta'] = $desde;
		}

		if (Funciones::esFechaMenor($hasta, $desde))
			throw new FactoryExceptionCustomException('La fecha "desde" no puede ser posterior a la fecha "hasta"');

		if (Funciones::diferenciaFechas($hasta, $desde) > $dias)
			throw new FactoryExceptionCustomException('El rango de fechas no puede superar los ' . $dias . ' días');
	}

	try {
		$array = array();

		//Armo el where
		$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha', true, true, 800);
		$where .= (empty($articulo) ? '' : ' AND cod_articulo = ' . Datos::objectToDB($articulo));
		$where .= (empty($cliente) ? '' : ' AND cod_cliente = ' . Datos::objectToDB($cliente));
		$where .= (empty($color) ? '' : ' AND cod_color_articulo = ' . Datos::objectToDB($color));
		$where .= ($empresa != 1 && $empresa != 2) ? '' : ' AND empresa = ' . Datos::objectToDB($empresa);
		$where .= ' GROUP BY cod_cliente, razon_social, cod_articulo, denom_articulo, cod_color_articulo';
		$where .= (empty($orderBy) ? '' : ' ORDER BY ' . $orderBy);

		$array[] = 'cod_cliente';
		$array[] = 'razon_social';
		$array[] = 'cod_articulo';
		$array[] = 'denom_articulo';
		$array[] = 'cod_color_articulo';
		$array[] = 'SUM(pares) as pares';
		$array[] = 'SUM(pares * ABS(precio_unitario_final)) as monto';

		$lista = Factory::getInstance()->getArrayFromView('reporte_articulos_v', $where, 0, $array);
		if(empty($lista)) {
			throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado');
		}

		$tabla = new HtmlTable(array('cantRows' => count($lista), 'cantCols' => 6, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
		$tabla->getRowCellArray($rows, $cells);

		armoHeadTabla($tabla);

		for ($i = 0; $i < count($lista); $i++) {
			$fila = $lista[$i];

			for ($j = 0; $j < $tabla->cantCols; $j++) {
				if ($j == 0) $cells[$i][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
				else $cells[$i][$j]->class .= ' bBottomDarkGray';
				if ($j == 5) $cells[$i][$j]->class .= ' bRightDarkGray bBottomDarkGray';
			}

			$cells[$i][0]->content = '[' . $fila['cod_cliente'] . '] ' . $fila['razon_social'];
			$cells[$i][1]->content = '[' . $fila['cod_articulo'] . '] ' . $fila['denom_articulo'];
			$cells[$i][2]->content = $fila['cod_color_articulo'];
			$cells[$i][3]->content = $fila['pares'];
			$cells[$i][4]->content = $fila['monto'];
			$cells[$i][5]->content = $fila['monto']/$fila['pares'];
		}

		$tabla->create();

	} catch (FactoryExceptionCustomException $ex) {
		Html::jsonInfo($ex->getMessage());
	} catch (Exception $ex) {
		Html::jsonNull();
	}

	?>
<?php } ?>