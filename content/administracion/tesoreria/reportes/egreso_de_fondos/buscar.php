<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/reportes/egreso_de_fondos/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$esXls = Funciones::get('esXls') == '1';

try {
	$where = Funciones::strFechas($desde, $hasta, 'fecha', true) . ' AND ';
	$where .= (empty($empresa) ? '' : 'empresa = ' . Datos::objectToDB($empresa));
	$where = trim($where, ' AND ');
	$orderBy = ' ORDER BY empresa ASC, fecha ASC, numero ASC';

	$listaEgresoDeFondos = Factory::getInstance()->getListObject('EgresoDeFondosItem', $where . $orderBy);

	if(count($listaEgresoDeFondos) == 0)
		throw new FactoryExceptionCustomException('No existen registros que cumplan con los filtros especificados.');

	$totales = array(
		'efectivo' => 0,
		'chequesPropios' => 0,
		'chequesTerceros' => 0,
		'transferencias' => 0,
		'total' => 0
	);

	$tiposRetencion = array();
	$arrayRetenciones = array();
	$i = 0;
	foreach($listaEgresoDeFondos as $item){
		/** @var EgresoDeFondosItem $item */
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
		array('content' => 'Beneficiario'),
		array('content' => 'Nro.<br>OP', 'dataType' => 'Center'),
		array('content' => 'Imp.<br>gen.', 'title' => 'Imputacion general'),
		array('content' => 'Imp. esp.', 'title' => 'Imputacion especifica'),
		array('content' => 'Fecha', 'dataType' => 'Center'),
		array('content' => 'Efvo.', 'title' => 'Efectivo', 'dataType' => 'Moneda'),
		array('content' => 'C. propios', 'dataType' => 'Moneda', 'title' => 'Cheques propios'),
		array('content' => 'C. terceros', 'dataType' => 'Moneda', 'title' => 'Cheques terceros'),
		array('content' => 'Transf.', 'title' => 'Transferencia bancaria', 'dataType' => 'Moneda')
	);

	foreach($tiposRetencion as $tipoRetencion){
		$arrayHeader[] = array('content' => 'Reten.<br>' . Funciones::toLower(Funciones::acortar(Factory::getInstance()->getTipoRetencion($tipoRetencion)->nombre, 14, '.')), 'dataType' => 'Moneda');
		$totales[$tipoRetencion->id] = 0;
	}

	$arrayHeader[] = array('content' => 'Total', 'dataType' => 'Moneda');

	$tabla = new HtmlTable(array('cantRows' => count($listaEgresoDeFondos), 'cantCols' => 10 + count($tiposRetencion), 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray($arrayHeader);

	$i = 0;
	foreach($listaEgresoDeFondos as $item){
		if($item->empresa == '1'){
			$rows[$i]->class .= ' bold';
		}
		$cells[$i][0]->content = Funciones::acortar($item->proveedor, 40);
		$cells[$i][0]->class .= 's11';
		$cells[$i][1]->content = $item->numeroOrdenDePago;
		$cells[$i][2]->content = ($item->ordenDePago->esAutonoma() ? $item->ordenDePago->imputacion->id : $item->imputacionGeneral . ($esXls ? ' - ' . $item->denomGeneral : ''));
		$cells[$i][3]->content = Funciones::acortar($item->ordenDePago->esAutonoma() ? $item->ordenDePago->imputacion->id . ' - ' . $item->ordenDePago->imputacion->nombre : $item->imputacionEspecifica . ' - ' . $item->denomEspecifica, 40);
		$cells[$i][3]->class .= 's11';
		$cells[$i][4]->content = $item->fecha;
		$cells[$i][5]->content = $item->efectivo;
		$cells[$i][6]->content = $item->chequesPropios;
		$cells[$i][7]->content = $item->chequesTerceros;
		$cells[$i][8]->content = $item->transferencias;

		$j = 9;
		foreach($tiposRetencion as $tipoRetencion){
			$cells[$i][$j++]->content = $arrayRetenciones[$i][$tipoRetencion];
			$totales[$tipoRetencion] += $arrayRetenciones[$i][$tipoRetencion];
		}
		$cells[$i][$j]->content = $item->total;

		$totales['efectivo'] += $item->efectivo;
		$totales['chequesPropios'] += $item->chequesPropios;
		$totales['chequesTerceros'] += $item->chequesTerceros;
		$totales['transferencias'] += $item->transferencias;
		$totales['total'] += $item->total;

		$i++;
	}

	$tabla->getFootArray($foots);
	$tabla->foot->tdBaseClass = 'bold white s14 p5 bLightOrange bTopWhite aRight ';
	$tabla->foot->tdBaseClassFirst = 'bold white s14 p5 bLightOrange bTopWhite aRight cornerBL5 ';
	$tabla->foot->tdBaseClassLast = 'bold white s14 p5 bLightOrange bTopWhite aRight cornerBR5 ';

	$foots[0]->content = 'TOTALES';
	$foots[0]->class .= ' aCenter';
	$foots[0]->colspan = 5;
	$foots[5]->content = Funciones::formatearMoneda($totales['efectivo']);
	$foots[6]->content = Funciones::formatearMoneda($totales['chequesPropios']);
	$foots[7]->content = Funciones::formatearMoneda($totales['chequesTerceros']);
	$foots[8]->content = Funciones::formatearMoneda($totales['transferencias']);
	$i = 9;
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