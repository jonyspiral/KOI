<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/reportes/cheques_propios/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$empresa = Funciones::get('empresa');
$estado = Funciones::get('estado');

try {
	switch($estado){
		case 1:
			//Esperando débito
			$whereEstado = 'esperando_en_banco = ' . Datos::objectToDB('D') . ' AND fecha_credito_debito IS NULL AND ';
			break;
		case 2:
			//En cartera
			$whereEstado = 'esperando_en_banco IS NULL AND fecha_credito_debito IS NULL AND ';
			break;
		default:
			//Todos
			break;
	}

	//Armo el where
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_vencimiento') . ' AND ';
	$where .= ($empresa != 1 && $empresa != 2) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= $whereEstado;
	$where = trim($where, ' AND ');
	$where = (empty($where) ? '1=1' : $where);
	$orderBy = ' ORDER BY fecha_vencimiento';

	$listaCheques = Factory::getInstance()->getArrayFromView('cheques_propios_v', $where . $orderBy);
	if(empty($listaCheques)) {
		throw new FactoryExceptionCustomException('No existen cheques con el filtro especificado');
	}

	$tabla = new HtmlTable(array('cantRows' => count($listaCheques), 'cantCols' => 7, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								'tdBaseClass' => 'bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'bBottomDarkGray bLeftDarkGray bRightDarkGray'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray(
		array(
			 array('content' => 'Fecha', 'dataType' => 'Center', 'width' => 10),
			 array('content' => 'Número', 'width' => 10),
			 array('content' => 'Banco', 'width' => 20),
			 array('content' => 'Estado', 'dataType' => 'Center', 'width' => 15),
			 array('content' => 'Entregado a', 'width' => 20),
			 array('content' => 'Días vto.', 'dataType' => 'Center', 'width' => 15),
			 array('content' => 'Importe', 'dataType' => 'Moneda', 'width' => 10)
		)
	);

	$totalEmpresa1 = 0;
	$totalEmpresa2 = 0;
	for ($i = 0; $i < count($listaCheques); $i++) {
		$cheque = $listaCheques[$i];

		for ($j = 0; $j < $tabla->cantCols; $j++) {
			if ($j == 0) $cells[$i][$j]->class .= ' bLeftDarkGray bBottomDarkGray';
			else $cells[$i][$j]->class .= ' bBottomDarkGray';
			if ($j == 6) $cells[$i][$j]->class .= ' bRightDarkGray bBottomDarkGray';
		}

		$estadoCheque = '-';
		if (empty($cheque['esperando_en_banco'])) {
			$estadoCheque = 'En cartera';
		} else {
			if(empty($cheque['fecha_credito_debito'])){
				$estadoCheque = 'Esperando débito';
			}else{
				$estadoCheque = 'Debitado';
			}
		}

		$cells[$i][0]->content = Funciones::formatearFecha($cheque['fecha_vencimiento'], 'd/m/Y');
		$cells[$i][1]->content = $cheque['numero'];
		$cells[$i][2]->content = $cheque['banco_nombre'];
		$cells[$i][3]->content = $estadoCheque;
		$cells[$i][4]->content = (empty($cheque['cod_prov']) ? '-' : '[' . $cheque['cod_prov'] . '] ' . $cheque['razon_social']);
		$cells[$i][5]->content = $cheque['dias'];
		$cells[$i][6]->content = $cheque['importe'];


		if($cheque['empresa'] == 1){
			$rows[$i]->class = 'bold';
			$totalEmpresa1 += $cheque['importe'];
		}else{
			$totalEmpresa2 += $cheque['importe'];
		}
	}

	$tablaTotales = new HtmlTable(array('cantRows' => 2, 'cantCols' => 1, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tablaTotales->getRowCellArray($rows, $cells);
	$cells[0][0]->content ='</br><label>' . ($empresa == '2' ? 'Saldo: ' : 'Total empresa 1: ') . Funciones::formatearMoneda($totalEmpresa1) . '</label>';
	$rows[0]->class = 'bold' . ($empresa == '2' ? ' hidden' : '');
	$cells[1][0]->content ='</br><label>' . ($empresa == '2' ? 'Saldo: ' : 'Total empresa 2: ') . Funciones::formatearMoneda($totalEmpresa2) . '</label>';
	$rows[1]->class = ($empresa == '1' ? ' hidden' : '');

	$htmlTablaTotales = $tablaTotales->create(true);
	$html = $tabla->create(true);

	echo $html . $htmlTablaTotales;

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
