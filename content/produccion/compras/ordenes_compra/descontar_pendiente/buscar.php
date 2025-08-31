<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/descontar_pendiente/buscar/')) { ?>
<?php

function divTablita($item){
	/** @var OrdenDeCompraItem $item */
	$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 8, 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->getHeadArray($ths);
	$tabla->getRowCellArray($rows, $cells);

	if($item->material->usaRango()){
		for ($i = 0; $i < $tabla->cantCols; $i++) {
			$talle = $item->material->rango->posicion[$i + 1];
			$ths[$i]->content = ($talle ? $talle : '-');
			$ths[$i]->class =  ($i % 2 ? 'w12p' : 'w13p');

			$cant = ($talle ? $item->cantidadesPendientes[$i + 1] : 0);
			$cells[0][$i]->content = '<input class="detalle_' . $item->idOrdenDeCompra . '_' . $item->numeroDeItem . ' textbox w25 aCenter inputPar" type="text" validate="Entero" value="' . Funciones::toInt($cant) . '" ';
			$cells[0][$i]->content .= 'data-idordendecompra="' . $item->idOrdenDeCompra . '" data-numerodeitem="' . $item->numeroDeItem . '" data-maxcant="' . $cant . '" />';
			$cells[0][$i]->class = 'aCenter';
		}
		$tabla->headerClass('tableHeader');

		return $tabla->create(true);
	} else {
		$cant = Funciones::formatearDecimales($item->cantidadPendiente, 4, '.');
		$div = '<div class="aCenter">';
		$div .= '<label>Cantidad: </label>';
		$div .= '<input class="detalle_' . $item->idOrdenDeCompra . '_' . $item->numeroDeItem . ' textbox w150 aCenter inputPar" type="text" validate="DecimalPositivo" value="' . $cant . '" ';
		$div .= 'data-idordendecompra="' . $item->idOrdenDeCompra . '" data-numerodeitem="' . $item->numeroDeItem . '" data-maxcant="' . $cant . '" />';
		$div .= '<label> ' . $item->material->unidadDeMedidaCompra->nombre . '</label>';
		$div .= '</div>';

		return $div;
	}
}

$idProveedor = Funciones::get('idProveedor');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$idOrdenDeCompra = Funciones::get('idOrdenDeCompra');
$numeroDeItem = Funciones::get('numeroDeItem');
$one = Funciones::get('one') == '1';

try {
	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'es_hexagono = ' . Datos::objectToDB('N');
	if (!$one) {
		$where .= ' AND cantidad_pendiente > 0';
		($fechaDesde || $fechaHasta) && $where .= ' AND ' . Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_salida_real');
		($idProveedor) && $where .= ' AND (cod_proveedor = ' . Datos::objectToDB($idProveedor) . ')';
		($idOrdenDeCompra) && $where .= ' AND (cod_orden_de_compra LIKE ' . Datos::objectToDB('%' . $idOrdenDeCompra . '%') . ')';
	} else {
		($idOrdenDeCompra) && $where .= ' AND (cod_orden_de_compra = ' . Datos::objectToDB($idOrdenDeCompra) . ')';
		($numeroDeItem) && $where .= ' AND (nro_item = ' . Datos::objectToDB($numeroDeItem) . ')';
	}
	$where = trim($where, ' AND ');

	$items = Factory::getInstance()->getListObject('OrdenDeCompraItem', $where);

	if (!$one) {
		if (!count($items)) {
			throw new FactoryExceptionCustomException('No hay ordenes de compra pendientes para los filtros especificados');
		}

		$tabla = new HtmlTable(array('cantRows' => count($items), 'cantCols' => 7, 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%'));
		$tabla->getRowCellArray($rows, $cells);
		$tabla->createHeaderFromArray(
			array(
				 array('content' => 'Proveedor', 'width' => 15),
				 array('content' => 'F. ent.', 'dataType' => 'Center', 'width' => 7, 'title' => 'Fecha entrega'),
				 array('content' => 'Nº<br>OC', 'dataType' => 'Center', 'width' => 3, 'title' => 'Númeor órden de compra'),
				 array('content' => 'Pos.', 'dataType' => 'Center', 'width' => 4, 'title' => 'Posición'),
				 array('content' => 'Material', 'dataType' => 'Center', 'width' => 20),
				 array('content' => 'Cantidad pendiente', 'dataType' => 'Center', 'width' => 46),
				 array('content' => '', 'dataType' => 'Center', 'width' => 5)
			)
		);
		$i = 0;
		foreach ($items as $item) {
			/** @var OrdenDeCompraItem $item */
			$cells[$i][0]->content = $item->ordenDeCompra->proveedor->getIdNombre();
			$cells[$i][1]->content = $item->fechaEntrega;
			$cells[$i][2]->content = $item->idOrdenDeCompra;
			$cells[$i][3]->content = $item->numeroDeItem;
			$cells[$i][4]->content = '[' . $item->colorMateriaPrima->material->id . ' - ' . $item->colorMateriaPrima->idColor . '] ' . $item->colorMateriaPrima->material->nombre . ' - ' . $item->colorMateriaPrima->nombreColor;
			$cells[$i][5]->content = divTablita($item);
			$cells[$i][6]->content = '<a href="#" class="boton btnConfirmar" title="Confirmar" style="display: inline;" ';
			$cells[$i][6]->content .= 'data-idordendecompra="' . $item->idOrdenDeCompra . '" data-numerodeitem="' . $item->numeroDeItem . '"><img src="/img/botones/25/aceptar.gif"></a>';

			$rows[$i]->id = 'row_' . $item->idOrdenDeCompra . '_' . $item->numeroDeItem;
			$i++;
		}

		$html = $tabla->create(true);
		echo $html;
	} else {
		if (count($items) > 1) {
			throw new FactoryExceptionCustomException('No se pudo actualizar correctamente el registro ya que devolvió múltiples filas');
		}
		Html::jsonEncode('', array(
								  'idOrdenDeCompra' => $idOrdenDeCompra,
								  'numeroDeItem' => $numeroDeItem,
								  'usaRango' => $items[0]->material->usaRango,
								  'pendiente' => Funciones::formatearDecimales($items[0]->cantidadPendiente, 4, '.'),
								  'pendientes' => $items[0]->cantidadesPendientes
							)
		);
	}
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>