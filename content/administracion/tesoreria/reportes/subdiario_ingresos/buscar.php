<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/reportes/subdiario_ingresos/buscar/')) { ?>
<?php

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$tipoRecibo = Funciones::get('tipoRecibo');
$idCliente = Funciones::get('idCliente');
$idVendedor = Funciones::get('idVendedor');
$empresa = Funciones::get('empresa');

try {
	$where = Funciones::strFechas($desde, $hasta, 'fecha', true) . ' AND ';
	$where .= (is_null($idCliente) ? '' : 'cod_cliente = ' . Datos::objectToDB($idCliente) . ' AND ');
	$where .= (is_null($idVendedor) ? '' : 'cod_vendedor = ' . Datos::objectToDB($idVendedor) . ' AND ');
	$where .= (empty($empresa) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ');
	$where .= ($tipoRecibo == 1 ? 'cod_cliente IS NOT NULL AND ' : ($tipoRecibo == 2 ? 'cod_cliente IS NULL AND ' : ''));
	$where = trim($where, ' AND ');
	$orderBy = ' ORDER BY empresa ASC, fecha ASC, numero ASC';

	$listaSubdiarioDeIngresos = Factory::getInstance()->getListObject('SubdiarioDeIngresosItem', $where . $orderBy);

	if(count($listaSubdiarioDeIngresos) == 0)
		throw new FactoryExceptionCustomException('No existen registros que cumplan con los filtros especificados.');

	$totales = array(
		'efectivo' => 0,
		'cheques' => 0,
		'transferencias' => 0,
		'total' => 0
	);

	$tiposRetencion = array();
	$arrayRetenciones = array();
	$i = 0;
	foreach($listaSubdiarioDeIngresos as $item){
		/** @var SubdiarioDeIngresosItem $item */
		$arrayRetenciones[$i] = array();
		foreach($item->getArrayRetenciones() as $key => $retencion){
			/** @var Retencion $retencion */
			if($retencion > 0){
				$tiposRetencion[$key] = $key;
				$arrayRetenciones[$i][$key] = $retencion;
			}
		}
		$i++;
	}

	$arrayHeader = array(
		array('content' => 'Recibido de'),
		array('content' => 'Nro.<br>REC', 'dataType' => 'Center'),
		array('content' => 'Imp', 'title' => 'Imputacion'),
		array('content' => 'Fecha', 'dataType' => 'Center'),
		array('content' => 'Efvo.', 'title' => 'Efectivo', 'dataType' => 'Moneda'),
		array('content' => 'Cheques', 'dataType' => 'Moneda'),
		array('content' => 'Transf.', 'title' => 'Transferencia bancaria', 'dataType' => 'Moneda')
	);

	foreach($tiposRetencion as $tipoRetencion){
		$arrayHeader[] = array('content' => 'Reten.<br>' . Funciones::toLower(Funciones::acortar(Factory::getInstance()->getTipoRetencion($tipoRetencion)->nombre, 14, '.')), 'dataType' => 'Moneda');
		$totales[$tipoRetencion->id] = 0;
	}

	$arrayHeader[] = array('content' => 'Total', 'dataType' => 'Moneda');

	$tabla = new HtmlTable(array('cantRows' => count($listaSubdiarioDeIngresos), 'cantCols' => 8 + count($tiposRetencion), 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray($arrayHeader);

	$i = 0;
	foreach($listaSubdiarioDeIngresos as $item){
		if($item->empresa == '1'){
			$rows[$i]->class .= ' bold';
		}
		$cells[$i][0]->content = Funciones::acortar($item->cliente, 40);
		$cells[$i][1]->content = $item->numeroRecibo;
		$cells[$i][2]->content = $item->imputacion;
		$cells[$i][3]->content = $item->fecha;
		$cells[$i][4]->content = $item->efectivo;
		$cells[$i][5]->content = $item->cheques;
		$cells[$i][6]->content = $item->transferencias;

		$j = 7;
		foreach($tiposRetencion as $tipoRetencion){
			$cells[$i][$j++]->content = $arrayRetenciones[$i][$tipoRetencion];
			$totales[$tipoRetencion] += $arrayRetenciones[$i][$tipoRetencion];
		}
		$cells[$i][$j]->content = $item->total;

		$totales['efectivo'] += $item->efectivo;
		$totales['cheques'] += $item->cheques;
		$totales['transferencias'] += $item->transferencias;
		$totales['total'] += $item->total;

		$i++;
	}

	$tabla->getFootArray($foots);
	$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
	$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
	$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';

	$foots[0]->content = 'TOTALES';
	$foots[0]->class .= ' aCenter';
	$foots[0]->colspan = 4;
	$foots[4]->content = Funciones::formatearMoneda($totales['efectivo']);
	$foots[5]->content = Funciones::formatearMoneda($totales['cheques']);
	$foots[6]->content = Funciones::formatearMoneda($totales['transferencias']);
	$i = 7;
	foreach($tiposRetencion as $tipoRetencion){
		$foots[$i++]->content = Funciones::formatearMoneda($totales[$tipoRetencion]);
	}
	$foots[$i++]->content = Funciones::formatearMoneda($totales['total']);

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