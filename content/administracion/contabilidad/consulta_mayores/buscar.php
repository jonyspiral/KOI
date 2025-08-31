<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/consulta_mayores/buscar/')) { ?>
<?php

$idImputacion = Funciones::get('idImputacion');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$fechaVtoDesde = Funciones::get('fechaVtoDesde');
$fechaVtoHasta = Funciones::get('fechaVtoHasta');
$empresa = Funciones::session('empresa');
$consolidado = Funciones::get('consolidado') == 'S';
$confirmar = (Funciones::get('confirmar') == '1');

function getSaldos($fechaDesde, $fechaInicial, $campoFecha, $stringImputacion) {
	global $empresa;

	$where = Datos::objectToDB($fechaInicial) . ' BETWEEN fecha_desde AND fecha_hasta';
	$where .= ' AND anulado = ' . Datos::objectToDB('N');

	$ejerciciosContables = Factory::getInstance()->getListObject('EjercicioContable', $where);
	$ejercicioContable = $ejerciciosContables[0];

	$where = (empty($empresa) ? '' : 'empresa = ' . Datos::objectToDB($empresa) .' AND ');
	$where .= $stringImputacion . ' AND ';

	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$fecha = Funciones::sumarTiempo(($fechaDesde ? $fechaDesde : $ejercicioContable->fechaDesde), -1);
	$where .= Funciones::strFechas($dummyVar, $fecha, $campoFecha);

	$fields = 'SUM(importe_debe) saldo_debe, SUM(importe_haber) saldo_haber';

	return Factory::getInstance()->getArrayFromView('filas_asientos_contables_v', $where, 0, $fields);
}

try {
	$fechaSql = Contabilidad::getFechaBusquedaReporte($fechaDesde, $fechaHasta, $fechaVtoDesde, $fechaVtoHasta, $hayQueConfirmar);

	$esFechaVto = ($fechaVtoDesde || $fechaVtoHasta);

	if (!$confirmar && $hayQueConfirmar) {
		Html::jsonConfirm('La búsqueda solicitada contiene resultados de más de un período contable. ¿Desea realizarla de todos modos?', 'confirmar');
	}else {
		if (empty($idImputacion)) {
			throw new FactoryExceptionCustomException('Debe especificar una cuenta');
		}

		$empresa = ($consolidado ? '' : $empresa);
		$imputacion = Factory::getInstance()->getImputacion($idImputacion);
		//$stringImputacion = $imputacion->getWhereSql('cod_imputacion');
		$stringImputacion = 'cod_imputacion = ' . Datos::objectToDB($imputacion->id);

		//Armo el where
		$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
		$where .= $stringImputacion . ' AND ';
		$where .= (empty($empresa) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ');
		$where .= $fechaSql . ' AND ';
		$where = trim($where, ' AND ');
		$orderBy = ' ORDER BY fecha_asiento ASC, cod_asiento ASC';

		$filasAsientosContables = Factory::getInstance()->getArrayFromView('filas_asientos_contables_v', $where . $orderBy);
		$cantidadFilas = count($filasAsientosContables);
		if (!$cantidadFilas) {
			throw new FactoryExceptionCustomException('No existen asientos con el filtro especificado');
		}

		$tabla = new HtmlTable(array('cantRows' => $cantidadFilas  + 2, 'cantCols' => 7, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%',
									'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray(
			array(
				 array('content' => 'Nº', 'dataType' => 'Center', 'title' => 'Nº de asiento', 'width' => 4),
				 array('content' => ($esFechaVto ? 'F. vto.' : 'F. asiento'), 'title' => ($esFechaVto ? 'Fecha de vencimiento' : 'Fecha de asiento'), 'width' => 10),
				 array('content' => 'Asunto', 'width' => 35),
				 array('content' => 'Debe', 'dataType' => 'Moneda', 'width' => 10),
				 array('content' => 'Haber', 'dataType' => 'Moneda', 'width' => 10),
				 array('content' => 'Observaciones', 'width' => 20),
				 array('content' => 'Saldo', 'dataType' => 'Moneda', 'width' => 11)
			)
		);

		$initVal = 0;
		$initVal++;
		$saldos = getSaldos(($esFechaVto ? $fechaVtoDesde : $fechaDesde), $filasAsientosContables[0]['fecha_asiento'], ($esFechaVto ? 'fecha_vencimiento' : 'fecha_asiento'), $stringImputacion);
		$saldoInicialDebe = $saldos[0]['saldo_debe'];
		$saldoInicialHaber = $saldos[0]['saldo_haber'];

		$saldo = $saldoInicialDebe - $saldoInicialHaber;

		for ($i = 0; $i < $cantidadFilas; $i++) {
			$k = $i + $initVal;
			$item = $filasAsientosContables[$i];

			$saldo += $item['importe_debe'] - $item['importe_haber'];

			$cells[$k][0]->content = $item['cod_asiento'];
			$cells[$k][1]->content = Funciones::formatearFecha(($esFechaVto ? $item['fecha_vencimiento'] : $item['fecha_asiento']), 'd/m/Y');
			$cells[$k][2]->content = (empty($item['asunto']) ? '-' : $item['asunto']);
			$cells[$k][3]->content = $item['importe_debe'] ? Funciones::formatearMoneda($item['importe_debe']) : '';
			$cells[$k][4]->content = $item['importe_haber'] ? Funciones::formatearMoneda($item['importe_haber']) : '';
			$cells[$k][5]->content = $item['observaciones'];
			$cells[$k][6]->content = $saldo;
		}

		$rows[0]->class = 'bDarkOrange white';

		$cells[0][0]->content = 'SALDO INICIAL';
		$cells[0][0]->colspan = 6;
		$cells[0][0]->class .= ' aCenter bold';
		$cells[0][6]->content = Funciones::formatearMoneda($saldoInicialDebe - $saldoInicialHaber);

		$j = $cantidadFilas + 1;
		$rows[$j]->class = 'bDarkOrange white';

		$cells[$j][0]->content = 'SALDO FINAL';
		$cells[$j][0]->colspan = 6;
		$cells[$j][0]->class .= ' aCenter bold';
		$cells[$j][6]->content = Funciones::formatearMoneda($saldo);

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