<?php require_once('../../../../premaster.php');  if (Usuario::logueado()->puede('administracion/contabilidad/plan_cuentas/buscar/')) { ?>
<?php

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$concepto = (Funciones::get('concepto') ? explode(' ', Funciones::get('concepto')) : array());
$esReporte = Funciones::get('$esReporte');

try {
	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= (empty($desde) ? '' : 'cuenta >= ' . $desde . ' AND ');
	$where .= (empty($hasta) ? '' : 'cuenta <= ' . $hasta . ' AND ');
	$where .= (count($concepto) > 0 ? '(' : '');
	foreach($concepto as $item){
		$where .= 'denominacion like ' . Datos::objectToDB('%' . $item . '%') . ' OR ';
	}
	if(count($concepto) > 0){
		$where = trim($where, ' OR ');
		$where .= ') AND ';
	}
	$where = trim($where, ' AND ');
	$order = ' ORDER BY cuenta ASC';

	$imputaciones = Factory::getInstance()->getListObject('Imputacion', $where . $order);
	if (count($imputaciones) == 0)
		throw new FactoryExceptionCustomException('No hay imputaciones con ese filtro');

	if($esReporte){
		$tabla = new HtmlTable(array('cantRows' => count($imputaciones), 'cantCols' => 3, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '99%',
									'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray(
			  array(
				   array('content' => 'Cuenta', 'dataType' => 'Center', 'width' => 10),
				   array('content' => 'Concepto', 'width' => 75),
				   array('content' => 'Imputable', 'dataType' => 'Center', 'width' => 15)
			  )
		);

		$i = 0;
		foreach ($imputaciones as $imputacion) {
			/** @var Imputacion $imputacion */

			$cells[$i][0]->content = $imputacion->id;
			$cells[$i][1]->content = $imputacion->nombre;
			$cells[$i][2]->content = ($imputacion->imputable == 'S' ? 'Imputable' : 'No imputable');

			$i++;
		}

		$tabla->create();
	} else {
		$arr = array();
		foreach ($imputaciones as $imputacion) {
			/** @var Imputacion $imputacion */
			$imp = array();

			$imp['cuenta'] = $imputacion->id;
			$imp['concepto'] = $imputacion->nombre;
			$imp['imputable'] = $imputacion->imputable;

			$arr[] = $imp;
		}
		Html::jsonEncode('', $arr);
	}
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>