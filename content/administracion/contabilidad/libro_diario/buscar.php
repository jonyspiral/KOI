<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/libro_diario/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$fechaVtoDesde = Funciones::get('fechaVtoDesde');
$fechaVtoHasta = Funciones::get('fechaVtoHasta');
$numeroDesde = Funciones::get('numeroDesde');
$numeroHasta = Funciones::get('numeroHasta');
$orden = Funciones::get('orden');
$empresa = Funciones::session('empresa');
$consolidado = Funciones::get('consolidado') == 'S';
$confirmar = (Funciones::get('confirmar') == '1');

try {
	$fechaSql = Contabilidad::getFechaBusquedaReporte($fechaDesde, $fechaHasta, $fechaVtoDesde, $fechaVtoHasta, $hayQueConfirmar);
	$usoVencimiento = ($fechaVtoDesde || $fechaVtoHasta);

	if (!$confirmar && $hayQueConfirmar) {
		Html::jsonConfirm('La búsqueda solicitada contiene resultados de más de un período contable. ¿Desea realizarla de todos modos?', 'confirmar');
	} else {
		$empresa = ($consolidado ? '' : $empresa);

		//Armo el where
		$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
		$where .= '(importe_debe > 0 OR importe_haber > 0) AND ';
		$where .= (is_null($numeroDesde) ? '' : 'cod_asiento >= ' . Datos::objectToDB($numeroDesde) . ' AND ');
		$where .= (is_null($numeroHasta) ? '' : 'cod_asiento <= ' . Datos::objectToDB($numeroHasta) . ' AND ');
		$where .= (empty($empresa) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ');
		$where .= $fechaSql . ' AND ';
		$where = trim($where, ' AND ');
		$groupBy = ' GROUP BY cod_asiento';
		$order = 'ORDER BY ';
		switch ($orden) {
			case 1: $order .= (($usoVencimiento ? 'fecha_vencimiento' : 'fecha_asiento') . ' DESC, cod_asiento DESC, numero_fila ASC'); break;
			case 2: $order .= 'cod_asiento ASC, numero_fila ASC'; break;
			case 3: $order .= 'cod_asiento DESC, numero_fila ASC'; break;
			default: $order .= (($usoVencimiento ? 'fecha_vencimiento' : 'fecha_asiento') . ' ASC, cod_asiento ASC, numero_fila ASC'); break;
		}
		$filasAsientosContables = Factory::getInstance()->getArrayFromView('filas_asientos_contables_v', $where . $order);
		$asientosContables = Factory::getInstance()->getArrayFromView('filas_asientos_contables_v', $where . $groupBy , null, 'cod_asiento');
		$cantidadFilas = count($filasAsientosContables);
		$cantidadAsientos = count($asientosContables);
		if (empty($cantidadFilas)) {
			throw new FactoryExceptionCustomException('No existen asientos con el filtro especificado');
		}

		$arrayHeader = array(array('content' => 'Nº', 'dataType' => 'Center', 'title' => 'Nº de asiento', 'width' => 4));

		if($consolidado){
			$arrayHeader[] = array('content' => 'E', 'dataType' => 'Center', 'width' => 2);
		}

		$arrayHeader = array_merge($arrayHeader,
								   array(
										array('content' => 'Fila', 'dataType' => 'Center', 'width' => 4),
										array('content' => 'F. ' . ($usoVencimiento ? 'vto.' : 'asi.'), 'title' => 'Fecha de ' . ($usoVencimiento ? 'vencimiento' : 'asiento'), 'width' => 10),
										array('content' => 'Imputación', 'width' => 30),
										array('content' => 'Debe', 'dataType' => 'Center', 'width' => 10),
										array('content' => 'Haber', 'dataType' => 'Center', 'width' => 10),
										array('content' => 'Observaciones', 'width' => 30)
								   ));

		$tabla = new HtmlTable(array('cantRows' => ($cantidadFilas + $cantidadAsientos), 'cantCols' => 7 + ($consolidado ? 1 : 0), 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%',
									'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray($arrayHeader);

		$asientoAnterior = false;
		$k = 0;
		for ($i = 0; $i < $cantidadFilas; $i++, $k++) {
			$j = 0;
			$item = $filasAsientosContables[$i];

			if ($asientoAnterior != $item['cod_asiento']) {
				$l = 0;
				$rows[$k]->class .= ' bold';
				$cells[$k][$l++]->content = '<br>' . $item['cod_asiento'];

				if($consolidado){
					$cells[$k][$l++]->content = '<br>' . $item['empresa'];
				}

				$cells[$k][$l]->content = '<br>' . Funciones::formatearFecha($item['fecha_asiento'], 'd/m/Y');
				$cells[$k][$l++]->colspan = 2;
				$cells[$k][$l++]->content = '<br>' . $item['asunto'];
				$cells[$k][$l++]->class .= ' aCenter';
				$cells[$k][$l]->content = '<br>' . Funciones::formatearMoneda($item['importe']);
				$cells[$k][$l++]->colspan = 2;
				$cells[$k][$l]->content = '<br>' . $item['observaciones'];
				$k++;
				$asientoAnterior = $item['cod_asiento'];
			}

			$cells[$k][$j++]->content = '';

			if($consolidado){
				$j++;
			}

			$cells[$k][$j++]->content = $item['numero_fila'];
			$cells[$k][$j++]->content = $usoVencimiento ? Funciones::formatearFecha($item['fecha_vencimiento'], 'd/m/Y') : '';
			$cells[$k][$j++]->content = '[' . $item['cod_imputacion'] . '] ' . trim($item['denominacion_imputacion']);
			$cells[$k][$j++]->content = $item['importe_debe'] ? Funciones::formatearMoneda($item['importe_debe']) : '';
			$cells[$k][$j++]->content = $item['importe_haber'] ? Funciones::formatearMoneda($item['importe_haber']) : '';
			$cells[$k][$j++]->content = $item['observaciones'];
		}

		$tabla->create();
	}
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}




?>
<?php } ?>