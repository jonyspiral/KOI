<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/finanzas/reportes/cliente/buscar/')) { ?>
<?php

	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$empresa = Funciones::get('empresa');

	function armoHeadTabla(HtmlTable &$tabla) {
		$tabla->getHeadArray($ths);
		$widths = array(30, 17, 5, 12, 12, 12, 12);
		for ($i = 0; $i < $tabla->cantCols; $i++) {
			$ths[$i]->style->width = $widths[$i] . '%';
			if ($i == 0) $ths[$i]->class = 'cornerL5';
			elseif ($i == $tabla->cantCols - 1) $ths[$i]->class = 'cornerR5 bLeftWhite';
			else $ths[$i]->class = 'bLeftWhite';
		}
		$tabla->headerClass('tableHeader');
		$ths[0]->content = 'Cliente';
		$ths[1]->content = 'Provincia';
		$ths[2]->content = 'Pares';
		$ths[2]->dataType = 'Entero';
		$ths[3]->content = 'Neto';
		$ths[3]->dataType = 'Moneda';
		$ths[4]->content = 'Iva';
		$ths[4]->dataType = 'Moneda';
		$ths[5]->content = 'Descuento';
		$ths[5]->dataType = 'Moneda';
		$ths[6]->content = 'Total';
		$ths[6]->dataType = 'Moneda';
		return $tabla;
	}

	try {
		$array = array();

		//Armo el where
		$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha', true, true, 800);
		$where .= ($empresa != 1 && $empresa != 2) ? '' : ' AND empresa = ' . Datos::objectToDB($empresa);
		$where .= ' GROUP BY cod_cliente, razon_social, provincia';
		$where .= ' ORDER BY total desc';

		$array[] = 'cod_cliente';
		$array[] = 'razon_social';
		$array[] = 'provincia';
		$array[] = 'SUM(pares) as pares';
		$array[] = 'SUM(neto) as neto';
		$array[] = 'SUM(iva) as iva';
		$array[] = 'SUM(descuento) as descuento';
		$array[] = 'SUM(total) as total';

		$lista = Factory::getInstance()->getArrayFromView('reporte_facturacion_v', $where, 10, $array);
		if(empty($lista)) {
			throw new FactoryExceptionCustomException('No existen documentos con el filtro especificado');
		}

		$tabla = new HtmlTable(array('cantRows' => count($lista), 'cantCols' => 7, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
		$tabla->getRowCellArray($rows, $cells);

		armoHeadTabla($tabla);

		for ($i = 0; $i < count($lista); $i++) {
			$fila = $lista[$i];

			for ($j = 0; $j < $tabla->cantCols; $j++) {
				if ($j == 0) $cells[$i][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
				else $cells[$i][$j]->class .= ' bBottomDarkGray';
				if ($j == 6) $cells[$i][$j]->class .= ' bRightDarkGray bBottomDarkGray';
			}

			$cells[$i][0]->content = '[' . $fila['cod_cliente'] . '] ' . $fila['razon_social'];
			$cells[$i][1]->content = $fila['provincia'];
			$cells[$i][2]->content = $fila['pares'];
			$cells[$i][3]->content = $fila['neto'];
			$cells[$i][4]->content = $fila['iva'];
			$cells[$i][5]->content = $fila['descuento'];
			$cells[$i][6]->content = $fila['total'];
		}

		$tabla->create();

	} catch (FactoryExceptionCustomException $ex) {
		Html::jsonInfo($ex->getMessage());
	} catch (Exception $ex) {
		Html::jsonNull();
	}

	?>
<?php } ?>