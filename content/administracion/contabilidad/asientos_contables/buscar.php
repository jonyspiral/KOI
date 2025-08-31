<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/asientos_contables/buscar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$asientoDesde = Funciones::get('asientoDesde');
$asientoHasta = Funciones::get('asientoHasta');
$orden = Funciones::get('orden');
$consolidado = Funciones::get('consolidado') == 'S';
$confirmar = (Funciones::get('confirmar') == '1');
$pdf = !!Funciones::get('pdf');

try {
	$empresa = ($consolidado ? '' : $empresa);

	$where = (empty($empresa) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ');
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$asientoDesde && ($where .= 'cod_asiento >= ' . Datos::objectToDB($asientoDesde) . ' AND ');
	$asientoHasta && ($where .= 'cod_asiento <= ' . Datos::objectToDB($asientoHasta) . ' AND ');
	$order = ' ORDER BY ';
	switch ($orden) {
		case 1: $order .= 'cod_asiento ASC'; break;
		case 2: $order .= 'fecha_asiento DESC, cod_asiento DESC'; break;
		case 3: $order .= 'fecha_asiento ASC, cod_asiento ASC'; break;
		default: $order .= 'cod_asiento DESC'; break;
	}

	if ($pdf) {
		$fechaSql = Contabilidad::getFechaBusquedaReporte($fechaDesde, $fechaHasta, $fechaVtoDesde, $fechaVtoHasta, $hayQueConfirmar);
		$usoVencimiento = ($fechaVtoDesde || $fechaVtoHasta);

		if (!$confirmar && $hayQueConfirmar) {
			Html::jsonConfirm('La búsqueda solicitada contiene resultados de más de un período contable. ¿Desea realizarla de todos modos?', 'confirmar');
		} else {
			$where = $fechaSql . ' AND ' . $where;
			$where = trim($where, ' AND ');
			$groupBy = 'cod_asiento, empresa, asunto, importe, fecha_asiento';
			$asientosContables = Factory::getInstance()->getArrayFromView('filas_asientos_contables_v', $where . ' GROUP BY ' . $groupBy . $order, null, $groupBy);

			$cantidadFilas = count($asientosContables);
			if (empty($cantidadFilas)) {
				throw new FactoryExceptionCustomException('No existen asientos con el filtro especificado');
			}

			$arrayHeader = array(array('content' => 'Fecha', 'dataType' => 'Fecha', 'width' => 10));

			if($consolidado){
				$arrayHeader[] = array('content' => 'E', 'dataType' => 'Center', 'width' => 3);
			}

			$arrayHeader = array_merge($arrayHeader,
				array(
					array('content' => 'Nº', 'dataType' => 'Center', 'width' => 6),
					array('content' => 'Asunto', 'width' => 67),
					array('content' => 'Importe', 'dataType' => 'Center', 'width' => 14)
				));

			$tabla = new HtmlTable(array('cantRows' => $cantidadFilas, 'cantCols' => 4 + ($consolidado ? 1 : 0), 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%',
										'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
			$tabla->getRowCellArray($rows, $cells);
			$tabla->createHeaderFromArray($arrayHeader);

			for ($i = 0; $i < $cantidadFilas; $i++) {
				$j = 0;
				$item = $asientosContables[$i];
				$cells[$i][$j++]->content = $item['fecha_asiento'];

				if($consolidado) {
					$cells[$i][$j++]->content = $item['empresa'];
				}

				$cells[$i][$j++]->content = $item['cod_asiento'];
				$cells[$i][$j++]->content = $item['asunto'];
				$cells[$i][$j++]->content = $item['importe'];
			}

			$tabla->create();
		}
	} else {
		$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_asiento') . ' AND ' . $where;
		$where = trim($where, ' AND ');
		$asientos = Factory::getInstance()->getListObject('AsientoContable', $where . $order);
		foreach($asientos as $asiento) {
			/** @var AsientoContable $asiento */
			foreach($asiento->detalle as $d) {
				/** @var FilaAsientoContable $d */
				$d->importeHaber = Funciones::toFloat($d->importeHaber, 2);
				$d->importeDebe = Funciones::toFloat($d->importeDebe, 2);
			}
			$asiento->importe = Funciones::toFloat($asiento->importe, 2);
			$asiento->expand();
		}
		Html::jsonEncode('', $asientos);
	}
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>