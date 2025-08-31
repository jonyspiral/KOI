<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/estadisticas/buscar/')) { ?>
<?php

function getItemsEstadistica() {
	global $modo, $desde, $hasta, $idVendedor, $idCliente, $tipoProducto, $idAlmacen, $idArticulo, $idColorMateriaPrima;

	$where = '1 = 1 ' . strFechas($desde, $hasta);
	if (isset($idVendedor)) $where .= ' AND cod_vendedor = ' . Datos::objectToDB($idVendedor);
	if (isset($idCliente)) $where .= ' AND cod_cliente = ' . Datos::objectToDB($idCliente);
	if (isset($idAlmacen)) $where .= ' AND cod_almacen = ' . Datos::objectToDB($idAlmacen);
	if (isset($idArticulo)) $where .= ' AND cod_articulo = ' . Datos::objectToDB($idArticulo);
	if (isset($idColorMateriaPrima)) $where .= ' AND cod_color_articulo = ' . Datos::objectToDB($idColorMateriaPrima);

	if (count($tipoProducto) > 0) {
		$tempWhere = '';
		foreach ($tipoProducto as $tipo)
			$tempWhere .= 'id_tipo_producto_stock = ' . Datos::objectToDB($tipo) . ' OR ';
		$where .= ' AND (' . trim($tempWhere, ' OR ') . ')';
	}

	$order = ' ORDER BY ';
	switch ($modo) {
		case 1: $order .= 'cod_vendedor ASC, cod_cliente ASC'; break;
		case 2: $order .= 'cod_almacen ASC, cod_articulo ASC, cod_color_articulo ASC'; break;
		case 3: $order .= 'cod_almacen ASC, cod_articulo ASC, cod_color_articulo ASC, cod_cliente ASC'; break;
		case 4: default: $order .= 'cod_cliente ASC, cod_almacen ASC, cod_articulo ASC, cod_color_articulo ASC'; break;
		case 5: $order .= 'cod_cliente ASC'; break;
	}
	$items = Factory::getInstance()->getListObject('PedidoItem', $where . $order);
	if (count($items) > 0) {
		$pedidosAnulados = array();

		$array = array();	//Array final que va a ser devuelto

		$i = 0;				//Contador de la lista de items

		while($i < count($items)) {

			$item = $items[$i];

			$arrIdC = getArrayIdC($modo, $item);

			$idC = $arrIdC['idC1'];
			$idCA = $idC;
			while ($idC === $idCA) {
				if (!in_array($item->numero, $pedidosAnulados)) {
					if ($item->pedido->anulado == 'S') {
						$pedidosAnulados[] = $item->numero;
					} else {
						//Meto el item en el array
						meterEnArray($array, $arrIdC, $item);
					}
				}

				//Inicializo el próximo item
				$i++;
				$item = $items[$i];
				$arrIdC = getArrayIdC($modo, $item);
				$idC = $arrIdC['idC1'];
			}
		}
		return $array;
	} else {
		throw new FactoryExceptionRegistroNoExistente('No hay pedidos con ese filtro');
	}
}

function comprobarFechas(&$desde, &$hasta) {
	$dias = 500;
	if (!isset($desde) && ! isset($hasta))
		throw new FactoryExceptionCustomException('Debe ingresar una fecha "desde" o una fecha "hasta"');

	if (!isset($desde))
		$desde = Funciones::sumarTiempo($hasta, -1 * $dias, 'days');
	if (!isset($hasta))
		$hasta = Funciones::sumarTiempo($desde, $dias, 'days');

	if (Funciones::esFechaMenor($hasta, $desde))
		throw new FactoryExceptionCustomException('La fecha "desde" no puede ser posterior a la fecha "hasta"');

	if (Funciones::diferenciaFechas($hasta, $desde) > $dias)
		throw new FactoryExceptionCustomException('El rango de fechas no puede superar los ' . $dias . ' días');
}

function strFechas($desde, $hasta, $campoFecha = 'fecha_pedido'){
	$strFechas = '';
	if (isset($desde) && isset($hasta)) {
		$strFechas = ' AND (' . $campoFecha . ' >= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($desde)) . ')';
		$strFechas .= ' AND ' . $campoFecha . ' <= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . '))';
	} elseif (isset($desde))
	$strFechas = ' AND (' . $campoFecha . ' >= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($desde)) . '))';
	elseif (isset($hasta))
	$strFechas = ' AND (' . $campoFecha . ' <= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . ')) ';
	return $strFechas;
}

function getDevueltos($filtros) {
	global $tipoProducto;
	$where = '(tipo_docum = \'NCR\') AND (cod_articulo IS NOT NULL) AND (cod_color_articulo IS NOT NULL) AND (anulado = \'N\') ';
	$where .= strFechas($filtros['desde'], $filtros['hasta'], 'fecha_documento');
	foreach($filtros as $filtro => $valor) {
		if ($filtro != 'desde' && $filtro != 'hasta') {
			if (is_scalar($valor)) {
				$where .= ' AND (' . $filtro . ' = ' . Datos::objectToDB($valor) . ')';
			} elseif (is_array($valor) && count($valor) > 0) {
				$tempWhere = '';
				foreach($valor as $orValor) {
					$tempWhere .= $filtro . ' = ' . Datos::objectToDB($orValor) . ' OR ';
				}
				$where .= ' AND (' . trim($tempWhere, ' OR ') . ')';
			}
		}
	}
	$items = Factory::getInstance()->getListObject('DocumentoItem', $where);
	$sum = 0;
	foreach($items as $item) {
		if (count($tipoProducto) == 0 || in_array($item->colorPorArticulo->idTipoProductoStock, $tipoProducto))
			$sum += $item->cantidadTotal;
	}
	return $sum;
}

function getDespachados($item) {
	return ($item->getTotalCantidad() - $item->getTotalPendiente() - $item->getTotalPredespachados() - $item->getTotalTickeados());
}

function getArrayIdC($modo, $item) {
	/** @var PedidoItem $item */
	if (is_null($item))
		return false;
	switch ($modo) {
		case 1: //ENCABEZADO: Vendedor || DETALLE: Cliente
			return array(
				'idC1' => $item->idVendedor,
				'idC2' => $item->idCliente,
				'titulo' => '[' . $item->vendedor->id . '] ' . $item->vendedor->nombreApellido,
				'tituloDetalle' => 'Cliente',
				'detalleItem' => '[' . $item->idCliente . '] ' . $item->cliente->razonSocial . '<label class="fRight pRight5">Calificación: ' . $item->cliente->calificacion . '</label>'
			);
		case 2: //ENCABEZADO: || DETALLE: Artículo
			return array(
				'idC1' => 0,
				'idC2' => $item->idAlmacen . '_' . $item->idArticulo . '_' . $item->idColorPorArticulo,
				'titulo' => '',
				'tituloDetalle' => 'Artículo',
				'detalleItem' => '[' . $item->getIdCombinado('-') . '] ' . $item->articulo->nombre . ' ' . $item->colorPorArticulo->nombre
			);
		case 3: //ENCABEZADO: Cliente || DETALLE: Artículo
			return array(
				'idC1' => $item->idCliente,
				'idC2' => $item->idAlmacen . '_' . $item->idArticulo . '_' . $item->idColorPorArticulo,
				'titulo' => '[' . $item->idCliente . '] ' . $item->cliente->razonSocial,
				'tituloDetalle' => 'Artículo',
				'detalleItem' => '[' . $item->getIdCombinado('-') . '] ' . $item->articulo->nombre . ' ' . $item->colorPorArticulo->nombre
			);
		case 4: //ENCABEZADO: Artículo || DETALLE: Cliente
		default:
			return array(
				'idC1' => $item->idAlmacen . '_' . $item->idArticulo . '_' . $item->idColorPorArticulo,
				'idC2' => $item->idCliente,
				'titulo' => '[' . $item->getIdCombinado('-') . '] ' . $item->articulo->nombre . ' ' . $item->colorPorArticulo->nombre,
				'tituloDetalle' => 'Cliente',
				'detalleItem' => '[' . $item->idCliente . '] ' . $item->cliente->razonSocial
			);
		case 5: //ENCABEZADO: || DETALLE: Cliente
			return array(
				'idC1' => 0,
				'idC2' => $item->idCliente,
				'titulo' => '',
				'tituloDetalle' => 'Cliente',
				'detalleItem' => '[' . $item->idCliente . '] ' . $item->cliente->razonSocial . '<label class="fRight pRight5">Calificación: ' . $item->cliente->calificacion . '</label>'
			);
	}
}

function getArrayFiltros($item) {
	global $desde, $hasta, $modo, $idVendedor, $idCliente, $idAlmacen, $idArticulo, $idColorMateriaPrima;
	$array = array();
	$array['desde'] = $desde;
	$array['hasta'] = $hasta;
	if (in_array($modo, array(1, 3, 4, 5)) || isset($idVendedor) || isset($idCliente)) {
		$array['cod_cliente'] = $item->idCliente;
	}
	if (in_array($modo, array(2, 3, 4)) || isset($idAlmacen) || isset($idArticulo) || isset($idColorMateriaPrima)) {
		$array['cod_almacen'] = $item->idAlmacen;
		$array['cod_articulo'] = $item->idArticulo;
		$array['cod_color_articulo'] = $item->idColorPorArticulo;
	}
	return $array;
}

function meterEnArray(&$array, $arrIdC, $item) {
	/** @var $item PedidoItem */
	$filtros = getArrayFiltros($item);
	if (!isset($array[$arrIdC['idC1']]))
		$array[$arrIdC['idC1']] = array(
				'titulo' => $arrIdC['titulo'],
				'tituloDetalle' => $arrIdC['tituloDetalle'],
				'detalle' => array()
		);
	if (!isset($array[$arrIdC['idC1']]['detalle'][$arrIdC['idC2']])) {
		$array[$arrIdC['idC1']]['detalle'][$arrIdC['idC2']] = array(
				'detalleItem' => $arrIdC['detalleItem'],
				'cantPedidos' => 0,
				'cantAnulados' => 0,
				'cantPredespachados' => 0,
				'cantPendientes' => 0,
				'cantDespachados' => 0,
				'cantDevueltos' => 0
		);
		//La cantidad devuelta de ese artículo debe setearse una única vez. El resto se va sumando
		$array[$arrIdC['idC1']]['detalle'][$arrIdC['idC2']]['cantDevueltos'] = getDevueltos($filtros);
	}
	$array[$arrIdC['idC1']]['detalle'][$arrIdC['idC2']]['cantPedidos'] += $item->getTotalCantidad();
	$array[$arrIdC['idC1']]['detalle'][$arrIdC['idC2']]['cantAnulados'] += $item->getTotalAnulados();
	$array[$arrIdC['idC1']]['detalle'][$arrIdC['idC2']]['cantPredespachados'] += ($item->anulado == 'N' ? $item->getTotalPredespachados() : 0);
	$array[$arrIdC['idC1']]['detalle'][$arrIdC['idC2']]['cantPendientes'] += ($item->anulado == 'N' ? $item->getTotalPendiente() : 0);
	$array[$arrIdC['idC1']]['detalle'][$arrIdC['idC2']]['cantDespachados'] += getDespachados($item);
}

function armoHead(&$tabla, $tituloEstadisticas) {
	//cornerL5
	$ths = array();
	$rows = array();
	//$tabla->getHeadArray($ths);
	for ($i = 0; $i < 2; $i++) {
		$rows[$i] = new HtmlTableRow();
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$ths[$i][$j] = new HtmlTableHead();
			if ($j == 0) $ths[$i][$j]->class = 'cornerL5 w50p';
			elseif (($j == $tabla->cantCols - 1) || ($i == 0 && $j == 6)) $ths[$i][$j]->class = 'cornerR5 w10p bLeftWhite';
			else $ths[$i][$j]->class = 'w10p bLeftWhite';
		}
	}
	
	$tabla->headerClass('tableHeader');
	
	$ths[0][0]->content = $tituloEstadisticas;
	$ths[0][0]->class = 'w100 cornerL5';
	$ths[0][1]->content = 'Pedidos';
	$ths[0][2]->content = 'Anulados';
	$ths[0][3]->content = 'Asignados';
	$ths[0][4]->content = 'Pendientes';
	$ths[0][5]->content = 'Facturados';
	$ths[0][6]->content = 'Devueltos';
	
	for ($i = 0; $i < 2; $i++) {
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$rows[$i]->addCell($ths[$i][$j]);
		}
	}
	
	$tabla->addHeadRow($rows[0]);
}

function armarTablaEstadistica(&$html, $tablas) {
	foreach($tablas as $tab) {
		
		$totalPedidos = 0;
		$totalAnulados = 0;
		$totalPredespachados = 0;
		$totalPendientes = 0;
		$totalDespachados = 0;
		$totalDevueltos = 0;
		
		$tabla = new HtmlTable(array('cantCols' => 7, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));

		armoHead($tabla, $tab['tituloDetalle']);
		$tabla->caption = ($tab['titulo'] != '' ? $tab['titulo'] : '');
		$tabla->captionClass ='s20';

		//***********************************************************************
		foreach($tab['detalle'] as $detalle) {
			$row = new HtmlTableRow();
			for($j = 0; $j < $tabla->cantCols; $j++) {
				$cells[$j] = new HtmlTableCell();
				$cells[$j]->class = 'pRight5 pLeft5';
				$cells[$j]->class .= ($j == 0 ? ' bAllDarkGray' : ' bTopDarkGray bBottomDarkGray bRightDarkGray aRight');
			}
			$cells[0]->content = $detalle['detalleItem'];
			$cells[1]->content = $detalle['cantPedidos'];
			$cells[2]->content = $detalle['cantAnulados'];
			$cells[3]->content = $detalle['cantPredespachados'];
			$cells[4]->content = $detalle['cantPendientes'];
			$cells[5]->content = $detalle['cantDespachados'];
			$cells[6]->content = $detalle['cantDevueltos'];
			$totalPedidos += $detalle['cantPedidos'];
			$totalAnulados += $detalle['cantAnulados'];
			$totalPredespachados += $detalle['cantPredespachados'];
			$totalPendientes += $detalle['cantPendientes'];
			$totalDespachados += $detalle['cantDespachados'];
			$totalDevueltos += $detalle['cantDevueltos'];
			
			for($j = 0; $j < $tabla->cantCols; $j++) {
				$cells[$j]->class ='bold bTopDarkGray bBottomDarkGray bRightDarkGray';
				$row->addCell($cells[$j]);
			}
			$tabla->addRow($row);
		}
		$fila = new HtmlTableRow();
		for ($i = 0; $i <= 6; $i++) {
			$celdaAux[$i] = new HtmlTableCell();
			$fila->addCell($celdaAux[$i]);
			$celdaAux[$i]->class = 'bLightOrange w70 bold bTopDarkGray bBottomDarkGray bold';
		}
		$celdaAux[0]->content = 'Totales: ';
		$celdaAux[1]->content = $totalPedidos;
		$celdaAux[2]->content = $totalAnulados;
		$celdaAux[3]->content = $totalPredespachados;
		$celdaAux[4]->content = $totalPendientes;
		$celdaAux[5]->content = $totalDespachados;
		$celdaAux[6]->content = $totalDevueltos;

		$tabla->addRow($fila);
				
		$html .= $tabla->create(true);
	}
}

//GET*********************************************************************
$modo = Funciones::get('modo');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$idVendedor = Funciones::get('idVendedor');
$idCliente = Funciones::get('idCliente');
$tipoProducto = (Funciones::get('tipoProducto') ? explode(',', Funciones::get('tipoProducto')) : array());
$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColorMateriaPrima = Funciones::get('idColor');

//POST********************************************************************

try {
	$html = '';
	comprobarFechas($desde, $hasta);
	$itemsEstadistica = getItemsEstadistica();
	if (count($itemsEstadistica) == 0)
		throw new FactoryException('No existen pedidos pendientes con ese filtro');
	armarTablaEstadistica($html, $itemsEstadistica);
	echo $html;
} catch (FactoryException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex){
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

} ?>