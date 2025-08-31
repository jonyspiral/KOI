<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/reportes/cheques_cartera/buscar/')) { ?>
<?php

function crearTabla() {
	$tabla = new HtmlTable(array('cantRows' => 0, 'cantCols' => 8, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->getRowCellArray($rows, $cells);

	$tabla->createHeaderFromArray(
		  array(
			   array('content' => 'Fecha', 'dataType' => 'Fecha', 'width' => 9),
			   array('content' => 'Número', 'dataType' => 'Right', 'width' => 10),
			   array('content' => 'Banco', 'width' => 18),
			   array('content' => 'Recibido de', 'width' => 18),
			   array('content' => 'Librador', 'width' => 18),
			   array('content' => 'CUIT', 'dataType' => 'Center', 'width' => 10),
			   array('content' => 'Días vto', 'dataType' => 'Center', 'width' => 8),
			   array('content' => 'Importe', 'dataType' => 'Moneda', 'width' => 9)
		  )
	);

	return $tabla;
}

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$empresa = Funciones::get('empresa');
$cliente = Funciones::get('cliente');
$orderBy = Funciones::get('orderBy');
$idCliente = Funciones::get('idCliente');

try {
	//Armo el where
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_vencimiento') . ' AND ';
	$where .= ($empresa != 1 && $empresa != 2) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= (empty($idCliente) ? '' : 'cod_cliente = ' . Datos::objectToDB($idCliente)) . ' AND ';
	$where = trim($where, ' AND ');
	$where = (empty($where) ? '1=1' : $where);
	$orderBy = ' ORDER BY fecha_vencimiento';

	$listaCheques = Factory::getInstance()->getArrayFromView('valores_en_cartera_v', $where . $orderBy);
	if(empty($listaCheques)) {
		throw new FactoryExceptionCustomException('No existen cheques con el filtro especificado');
	}

	$html = '';
	$totalEmpresa1 = 0;
	$totalEmpresa2 = 0;
	$i = 0;
	$cheque = $listaCheques[0];
	while ($i < count($listaCheques)) {
		$subtotalEmpresa1 = 0;
		$subtotalEmpresa2 = 0;
		$tabla = crearTabla();
		$tabla->caption = Funciones::formatearFecha($cheque['fecha_vencimiento'], 'd/m/Y');
		$tabla->captionClass ='s20';

		/** @var Predespacho $item */
		$cheque = $listaCheques[$i];
		$esPrimero = true;
		while ($esPrimero || ($idAnterior == Funciones::formatearFecha($cheque['fecha_vencimiento'], 'm/Y'))) {
			$esPrimero = false;
			$idAnterior = Funciones::formatearFecha($cheque['fecha_vencimiento'], 'm/Y');

			$row = new HtmlTableRow();
			for($j = 0; $j < $tabla->cantCols; $j++) {
				$cells[$j] = new HtmlTableCell();
				$cells[$j]->class = 'pRight5 pLeft5';
				if ($j == 0) {
					$cells[$j]->class .= ' bAllDarkGray';
				} else {
					$cells[$j]->class .= ' bTopDarkGray bBottomDarkGray bRightDarkGray';
				}
			}

			$cells[0]->content = Funciones::formatearFecha($cheque['fecha_vencimiento'], 'd/m/Y');
			$cells[1]->content = $cheque['numero'];
			$cells[2]->content = $cheque['banco_nombre'];
			$cells[3]->content = Funciones::acortar((empty($cheque['cod_cliente']) ? '-' : '[' . $cheque['cod_cliente'] . '] ' . $cheque['razon_social']), 24);
			$cells[4]->content = (empty($cheque['librador_nombre']) ? '-' : $cheque['librador_nombre']);
			$cells[5]->content = (empty($cheque['librador_cuit']) ? '-' : Funciones::ponerGuionesAlCuit($cheque['librador_cuit']));
			$cells[6]->content = $cheque['dias'];
			$cells[7]->content = $cheque['importe'];


			if($cheque['empresa'] == 1){
				$row->class = 'bold';
				$subtotalEmpresa1 += $cheque['importe'];
			}else{
				$subtotalEmpresa2 += $cheque['importe'];
			}

			for($j = 0; $j < $tabla->cantCols; $j++) {
				$row->addCell($cells[$j]);
			}
			$tabla->addRow($row);

			$i++;
			$cheque = $listaCheques[$i];
		}
		$tabla->getFootArray($foots);
		$tabla->foot->tdBaseClass = 'bold white s16 p5 bLightOrange bTopWhite aRight ';
		$tabla->foot->tdBaseClassFirst = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBL5 ';
		$tabla->foot->tdBaseClassLast = 'bold white s16 p5 bLightOrange bTopWhite aRight cornerBR5 ';

		$foots[0]->content = 'PARCIALES';
		$foots[0]->class .= ' aCenter';
		$foots[3]->class .= ' aCenter';
		$foots[5]->class .= ' aCenter';
		$foots[0]->colspan = 3;
		$foots[3]->colspan = 2;
		$foots[5]->colspan = 3;
		$foots[3]->content = ($empresa == '2' ? 'Saldo: ' : 'Empresa 1: ') . Funciones::formatearMoneda($subtotalEmpresa1);
		$foots[5]->content = ($empresa == '2' ? 'Saldo: ' : 'Empresa 2: ') . Funciones::formatearMoneda($subtotalEmpresa2);

		$totalEmpresa1 += $subtotalEmpresa1;
		$totalEmpresa2 += $subtotalEmpresa2;

		$html .= $tabla->create();
	}

	$tablaTotales = new HtmlTable(array('cantRows' => 2, 'cantCols' => 1, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tablaTotales->getRowCellArray($rows, $cells);
	$cells[0][0]->content ='</br><label>' . ($empresa == '2' ? 'Saldo: ' : 'Total empresa 1: ') . Funciones::formatearMoneda($totalEmpresa1) . '</label>';
	$rows[0]->class = 'bold' . ($empresa == '2' ? ' hidden' : '');
	$cells[1][0]->content ='</br><label>' . ($empresa == '2' ? 'Saldo: ' : 'Total empresa 2: ') . Funciones::formatearMoneda($totalEmpresa2) . '</label>';
	$rows[1]->class = ($empresa == '1' ? ' hidden' : '');

	$html .= $tablaTotales->create();

	echo $html;

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
