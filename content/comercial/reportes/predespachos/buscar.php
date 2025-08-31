<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/reportes/predespachos/buscar/')) { ?>
<?php

function crearTabla($cantRows = 1) {
	$tabla = new HtmlTable(array('cantRows' => $cantRows, 'cantCols' => 13, 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray'));
	$tabla->createHeaderFromArray(
		  array(
			   array('content' => 'Fecha', 'dataType' => 'Center', 'width' => 8),
			   array('content' => 'Artículo', 'width' => 30),
			   array('content' => 'Color', 'dataType' => 'Center', 'width' => 5),
			   array('content' => 'Rango', 'dataType' => 'Center', 'width' => 10),
			   array('content' => 'C1', 'dataType' => 'Entero', 'width' => 4),
			   array('content' => 'C2', 'dataType' => 'Entero', 'width' => 4),
			   array('content' => 'C3', 'dataType' => 'Entero', 'width' => 4),
			   array('content' => 'C4', 'dataType' => 'Entero', 'width' => 4),
			   array('content' => 'C5', 'dataType' => 'Entero', 'width' => 4),
			   array('content' => 'C6', 'dataType' => 'Entero', 'width' => 4),
			   array('content' => 'C7', 'dataType' => 'Entero', 'width' => 4),
			   array('content' => 'C8', 'dataType' => 'Entero', 'width' => 4),
			   array('content' => 'Total', 'dataType' => 'Entero', 'width' => 5)
		  )
	);

	return $tabla;
}

$empresa = Funciones::session('empresa');
$tipo = Funciones::get('tipo');
$idCliente = Funciones::get('idCliente');
$idPedido = Funciones::get('idPedido');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$almacen = Funciones::get('almacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	$strFechas = Funciones::strFechas($desde, $hasta, 'fecha_alta');

	$where = 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= (empty($strFechas) ? '' : $strFechas . ' AND ');
	$where .= (empty($almacen) ? '' : 'cod_almacen = ' . Datos::objectToDB($almacen) . ' AND ');
	$where .= (empty($idArticulo) ? '' : 'cod_articulo = ' . Datos::objectToDB($idArticulo) . ' AND ');
	$where .= (empty($idColor) ? '' : 'cod_color = ' . Datos::objectToDB($idColor) . ' AND ');

	$html = '';
	if ($tipo == 'C') {
		$where .= 'predespachados > 0 AND ';
		$where .= (empty($idCliente) ? '' : 'cod_cliente = ' . Datos::objectToDB($idCliente) . ' AND ');
		$where = trim($where, ' AND ');
		$order = ' ORDER BY cod_cliente DESC';

		$items = Factory::getInstance()->getListObject('Predespacho', $where . $order);
		if (count($items) == 0) {
			throw new FactoryExceptionCustomException('No hay predespachos con ese filtro');
		}

		$i = 0;
		while ($i < count($items)) {
			$tabla = crearTabla();
			$cliente = $items[$i]->cliente;
			$tabla->caption = $cliente->getIdNombre();
			$tabla->captionClass ='s20';

			/** @var Predespacho $item */
			$item = $items[$i];
			$esPrimero = true;
			while ($esPrimero || ($idAnterior == $item->idCliente)) {
				$esPrimero = false;
				$idAnterior = $item->idCliente;

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

				$cells[0]->content = $item->fechaAlta;
				$cells[1]->content = $item->articulo->getIdNombre();
				$cells[2]->content = $item->idColorPorArticulo;
				$cells[3]->content = $item->articulo->rangoTalle->posicionInicial . ' - ' . $item->articulo->rangoTalle->posicionFinal;
				for ($k = 1; $k < 9; $k++) {
					$cells[3 + $k]->content = $item->predespachados[$k];
				}
				$cells[3 + $k]->content = $item->getTotalPredespachados();

				for($j = 0; $j < $tabla->cantCols; $j++) {
					$row->addCell($cells[$j]);
				}
				$tabla->addRow($row);

				$i++;
				$item = $items[$i];
			}
			$html .= $tabla->create();
		}
	} else {
		if(empty($idPedido)) {
			throw new FactoryExceptionCustomException('Debe especificar un pedido');
		}

		$where .= (empty($idPedido) ? '' : 'nro_pedido = ' . Datos::objectToDB($idPedido) . ' AND ');
		$where = trim($where, ' AND ');

		$items = Factory::getInstance()->getListObject('Predespacho', $where . $order);
		if (count($items) == 0) {
			throw new FactoryExceptionCustomException('No hay predespachos con ese filtro');
		}

		$tabla = crearTabla(count($items));
		$tabla->getRowCellArray($rows, $cells);

		for ($i = 0; $i < count($items); $i++) {
			$item = $items[$i];

			$cells[0]->content = $item->fechaAlta;
			$cells[1]->content = $item->articulo->getIdNombre();
			$cells[2]->content = $item->idColorPorArticulo;
			$cells[3]->content = $item->articulo->rangoTalle->posicionInicial . ' - ' . $item->articulo->rangoTalle->posicionFinal;
			for ($k = 1; $k < 9; $k++) {
				$cells[3 + $k]->content = $item->predespachados[$k];
			}
			$cells[3 + $k]->content = $item->getTotalPredespachados();
		}
	}

	/*$strFechas = Funciones::strFechas($desde, $hasta, 'fecha_alta');

	$where = 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= (empty($strFechas) ? '' : $strFechas);
	$where .= (empty($almacen) ? '' : 'cod_almacen = ' . Datos::objectToDB($almacen) . ' AND ');
	$where .= (empty($idArticulo) ? '' : 'cod_articulo = ' . Datos::objectToDB($idArticulo) . ' AND ');
	$where .= (empty($idColor) ? '' : 'cod_color = ' . Datos::objectToDB($idColor) . ' AND ');
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_alta DESC';

	$items = Factory::getInstance()->getListObject('Predespacho', $where . $order);
	if (count($items) == 0)
		throw new FactoryExceptionCustomException('No hay predespachos con ese filtro');

	$arr = array();
	foreach ($items as $item) {
		$arr[] = jsonPredespacho($item);
	}
	Html::jsonEncode('', $arr);*/
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>