<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/pendiente/buscar/')) { ?>
<?php

function armarTitulo(OrdenDeCompra $ordenDeCompra, $pdf) {
	$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 5, 'class' => '', 'cellSpacing' => 1, 'width' => '100%'));
	if ($pdf) {
		$tabla->body->tdBaseClass = 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray';
		$tabla->body->tdBaseClassLast = 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray';
	}
	$tabla->getRowCellArray($rows, $cells);
	if ($pdf) {
		$tabla->createHeaderFromArray(
			array(
				 array('content' => 'F. emisión', 'dataType' => 'Fecha', 'width' => 10),
				 array('content' => 'Número', 'dataType' => 'Center', 'width' => 10),
				 array('content' => 'Proveedor', 'width' => 60),
				 array('content' => 'Cant. detalles', 'dataType' => 'Center', 'width' => 10),
				 array('content' => 'Importe pend.', 'dataType' => 'Moneda', 'width' => 10)
			)
		);
		$tabla->headClass('tableHeader');
	}

	$rows[0]->class = '';

	$cells[0][0]->class = 'w10p';
	$cells[0][1]->class = 'w10p';
	$cells[0][2]->class = 'w60p';
	$cells[0][3]->class = 'w10p';
	$cells[0][4]->class = 'w10p';

	$cells[0][0]->content = $ordenDeCompra->fechaEmision;
	$cells[0][0]->class .= ' aCenter';
	$cells[0][1]->content = $ordenDeCompra->id;
	$cells[0][2]->content = '[' . $ordenDeCompra->proveedor->id . '] ' . $ordenDeCompra->proveedor->razonSocial;
	$cells[0][3]->content = count($ordenDeCompra->detalle);
	$cells[0][3]->class .= ' aCenter';
	$cells[0][4]->content = Funciones::formatearMoneda($ordenDeCompra->importePendiente);
	$cells[0][4]->class .= ' aRight';

	return $tabla->create(true);
}

$idProveedor = Funciones::get('idProveedor');
$idLoteDeProduccion = Funciones::get('idLoteDeProduccion');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$orderBy = Funciones::get('orderBy');
$pdf = Funciones::get('pdf');

try{
	$html = '';
	$strFecha = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_emision');
	$where = 'pendiente > ' . Datos::objectToDB(ParametrosCompras::minimoParaConsiderarCumplido) . ' AND ';
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'es_hexagono = ' . Datos::objectToDB('N') . ' AND ';
	$where .= (empty($strFecha) ? '' : $strFecha . ' AND ');
	$where .= (empty($idProveedor) ? '' : 'cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' AND ');
	$where .= (empty($idLoteDeProduccion) ? '' : 'nro_lote = ' . Datos::objectToDB($idLoteDeProduccion) . ' AND ');
	$where = trim($where, ' AND ');
	$orderBy = (empty($orderBy) || empty($where) ? '' : ' ORDER BY ' . $orderBy);

	$ordenesDeCompra = Factory::getInstance()->getListObject('OrdenDeCompra', $where . $orderBy);
	if(empty($ordenesDeCompra)) {
		throw new FactoryExceptionCustomException('No existen órdenes de pago pendientes con el filtro especificado');
	}


	foreach($ordenesDeCompra as $ordenDeCompra) {
		/** @var OrdenDeCompra $ordenDeCompra */
		$cantidadDetalles = 0;
		foreach($ordenDeCompra->detalle as $item) {
			if($item->cantidadPendiente > ParametrosCompras::minimoParaConsiderarCumplido) {
				$cantidadDetalles++;
			}
		}

		if($cantidadDetalles > 0) {
			$i = 0;
			$tabla = new HtmlTable(array('cantRows' => $cantidadDetalles, 'cantCols' => 5, 'class' => ($pdf ? 'pBottom30 ' : '') . 'tableFontSize', 'cellSpacing' => 1, 'width' => '100%',
										'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));

			$tabla->createHeaderFromArray(
				array(
					 array('content' => 'F. entr.', 'dataType' => 'Fecha', 'width' => 6, 'title' => 'Fecha entrega'),
					 array('content' => 'Material', 'dataType' => 'Left', 'width' => 30),
					 array('content' => 'Color', 'dataType' => 'Left', 'width' => 10),
					 array('content' => 'Pendientes', 'width' => 44),
					 array('content' => 'Importe pend.', 'width' => 10, 'dataType' => 'Moneda')
				)
			);
			$tabla->getRowCellArray($rows, $cells);
			$rows[$i]->class .= ' s11';

			foreach($ordenDeCompra->detalle as $item) {
				if($item->cantidadPendiente > ParametrosCompras::minimoParaConsiderarCumplido) {
					$cells[$i][0]->content = Funciones::formatearFecha($item->fechaEntrega);
					$cells[$i][1]->content = '[' . $item->material->id . '] ' . $item->material->nombre;
					$cells[$i][2]->content = '[' . $item->colorMateriaPrima->idColor . '] ' . $item->colorMateriaPrima->nombreColor;

					if($item->material->usaRango()) {
						$tablaTalles = new HtmlTable(array('cantRows' => 1, 'cantCols' => 11, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
														  'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));

						$tablaTalles->getRowCellArray($rowsTalles, $cellsTalles);
						$rowsTalles[0]->class .= ' s11';
						$arrayHeader = array();
						$total = 0;
						for($j = 0; $j < 10; $j++){
							$arrayHeader[] = array('content' => $item->material->rango->posicion[$j + 1], 'dataType' => 'Center');
							$cellsTalles[0][$j]->content = Funciones::toInt($item->cantidadesPendientes[$j + 1]);
							$total += Funciones::toInt($item->cantidadesPendientes[$j + 1]);
						}
						$arrayHeader[] = array('content' => 'Total', 'dataType' => 'Center');
						$cellsTalles[0][10]->content = Funciones::toInt($total);

						$tablaTalles->createHeaderFromArray($arrayHeader);

						$cells[$i][3]->content = $tablaTalles->create(true);
					}else {
						$cells[$i][3]->content = Funciones::formatearDecimales($item->cantidadPendiente, 4);
						$cells[$i][3]->class .= ' aCenter';
					}

					$cells[$i][4]->content = $item->importePendiente;

					$i++;
				}
			}

			$html .= '<div id="">
	 				  <div class="' . $class . '" style="color: black; font-size: 13px; padding-left: 3px;">' . armarTitulo($ordenDeCompra, $pdf) . '</div>
				      <div>' . $tabla->create(true) . '</div>
					  </div>';
		}
	}

	echo $html;

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>
