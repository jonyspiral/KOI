<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/sumas_saldos/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
//$fechaVtoDesde = Funciones::get('fechaVtoDesde');
//$fechaVtoHasta = Funciones::get('fechaVtoHasta');
$imputacionDesde = Funciones::get('imputacionDesde');
$imputacionHasta = Funciones::get('imputacionHasta');
$empresa = Funciones::session('empresa');
$consolidado = Funciones::get('consolidado') == 'S';
$confirmar = (Funciones::get('confirmar') == '1');

try {
	$empresa = ($consolidado ? '' : $empresa);
	$fechaSql = Contabilidad::getFechaBusquedaReporte($fechaDesde, $fechaHasta, $fechaVtoDesde, $fechaVtoHasta, $hayQueConfirmar);

	if (!$confirmar && $hayQueConfirmar) {
		Html::jsonConfirm('La búsqueda solicitada contiene resultados de más de un período contable. ¿Desea realizarla de todos modos?', 'confirmar');
	}else {
		$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
		$where .= (is_null($imputacionDesde) ? '' : 'cod_imputacion >= ' . Datos::objectToDB($imputacionDesde) . ' AND ');
		$where .= (is_null($imputacionHasta) ? '' : 'cod_imputacion <= ' . Datos::objectToDB($imputacionHasta) . ' AND ');
		$where .= (empty($empresa) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ');
		$where .= $fechaSql . ' AND ';
		$where = trim($where, ' AND ');
		$where .= ' GROUP BY cod_imputacion, denominacion_imputacion';
		$orderBy = ' ORDER BY cod_imputacion ASC';

		$fields = 'cod_imputacion, denominacion_imputacion, SUM(importe_debe) saldo_debe, SUM(importe_haber) saldo_haber';

		$filasAsientosContables = Factory::getInstance()->getArrayFromView('filas_asientos_contables_v', $where . $orderBy, 0, $fields);

		$cantidadFilas = count($filasAsientosContables);
		if (empty($cantidadFilas)) {
			throw new FactoryExceptionCustomException('No existen asientos con el filtro especificado');
		}

		$tabla = new HtmlTable(array('cantRows' => $cantidadFilas, 'cantCols' => 5, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%',
									'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray(
			array(
				 array('content' => 'Imputación', 'dataType' => 'Center', 'width' => 10),
				 array('content' => 'Nombre imputación', 'width' => 45),
				 array('content' => 'Debe', 'dataType' => 'Moneda', 'width' => 15),
				 array('content' => 'Haber', 'dataType' => 'Moneda', 'width' => 15),
				 array('content' => 'Saldo', 'dataType' => 'Moneda', 'width' => 15)
			)
		);

		for ($i = 0; $i < $cantidadFilas; $i++) {
			$item = $filasAsientosContables[$i];

			$cells[$i][0]->content = $item['cod_imputacion'];
			$cells[$i][1]->content = $item['denominacion_imputacion'];
			$cells[$i][2]->content = $item['saldo_debe'];
			$cells[$i][3]->content = $item['saldo_haber'];
			$cells[$i][4]->content = $item['saldo_debe'] - $item['saldo_haber'];
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