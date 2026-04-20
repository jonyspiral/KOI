<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/reportes/cheques_rechazados/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$fechaVtoDesde = Funciones::get('fechaVtoDesde');
$fechaVtoHasta = Funciones::get('fechaVtoHasta');
$empresa = Funciones::get('empresa');
$orderBy = Funciones::get('orderBy');
$idCliente = Funciones::get('idCliente');
$numero = Funciones::get('numero');
$librador = Funciones::get('librador');

try {
	//Armo el where
	$strFecha = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha');
	$strFechaVto = Funciones::strFechas($fechaVtoDesde, $fechaVtoHasta, 'fecha_vencimiento');

	$where = (empty($strFecha) ? '' : $strFecha . ' AND ');
	$where .= (empty($strFechaVto) ? '' : $strFechaVto . ' AND ');
	$where .= ($empresa != 1 && $empresa != 2) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= (empty($idCliente) ? '' : 'cod_cli = ' . Datos::objectToDB($idCliente)) . ' AND ';
	$where .= (empty($numero) ? '' : 'numero LIKE ' . Datos::objectToDB('%' . $numero . '%')) . ' AND ';
	$where .= (empty($librador) ? '' : 'librador_nombre LIKE ' . Datos::objectToDB('%' . $librador . '%')) . ' AND ';
	$where = trim($where, ' AND ');
	$orderBy = (empty($where) ? '1 = 1' : '') . ' ORDER BY ' . $orderBy;

	$listaCheques = Factory::getInstance()->getArrayFromView('cheques_rechazados_v', $where . $orderBy);
	if (empty($listaCheques)) {
		throw new FactoryExceptionCustomException('No existen cheques con el filtro especificado');
	}

	$tabla = new HtmlTable(array(
								'cantRows'    => count($listaCheques), 'cantCols' => 11, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray s11', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray s11'
						   ));
	$tabla->getRowCellArray($rows, $cells);

	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'F. Rech.', 'dataType' => 'Center', 'width' => 6),
			 array('content' => 'F. Vto.', 'dataType' => 'Center', 'width' => 6),
			 array('content' => 'E', 'dataType' => 'Center', 'width' => 2),
			 array('content' => 'Cliente', 'width' => 14),
			 array('content' => 'Librador', 'width' => 14),
			 array('content' => 'Vendedor', 'width' => 8),
			 array('content' => 'Banco', 'width' => 10),
			 array('content' => 'Número', 'dataType' => 'Center', 'width' => 7),
			 array('content' => 'Motivo rech', 'width' => 16),
			 array('content' => 'Observaciones', 'width' => 11),
			 array('content' => 'Importe', 'dataType' => 'Moneda', 'width' => 6)
		)
	);

	for ($i = 0; $i < count($listaCheques); $i++) {
		$rows[$i]->class = '';
		$cheque = $listaCheques[$i];

		$cells[$i][0]->content = Funciones::formatearFecha($cheque['fecha'], 'd/m/Y');
		$cells[$i][1]->content = Funciones::formatearFecha($cheque['fecha_vencimiento'], 'd/m/Y');
		$cells[$i][2]->content = $cheque['empresa'];
		$cells[$i][3]->content = (empty($cheque['cod_cli']) ? '-' : '[' . $cheque['cod_cli'] . '] ' . $cheque['cliente_razon_social']);
		$cells[$i][4]->content = $cheque['librador_nombre'];
		$cells[$i][5]->content = (empty($cheque['cod_vendedor']) ? '-' : $cheque['nombre_vendedor']);
		$cells[$i][6]->content = $cheque['banco_nombre'];
		$cells[$i][7]->content = $cheque['numero'];
		$cells[$i][8]->content = $cheque['nombre_motivo'];
		$cells[$i][9]->content = (empty($cheque['observaciones']) ? '-' : $cheque['observaciones']);
		$cells[$i][10]->content = $cheque['importe'];
	}

	$html = $tabla->create(true);

	echo $html;

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
