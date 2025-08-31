<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/ecommerce/panel_de_control/buscar/')) { ?>
<?php

function tabsInfo(){
	$where = 'anulado = ' . Datos::objectToDB('N') . ' GROUP BY cod_status';
	$array = Factory::getInstance()->getArrayFromView('ecommerce_orders', $where, 0, 'cod_status, SUM(grand_total) total, SUM(1) cantidad_pedidos');
	return $array;
}

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$idStatus = Funciones::get('idStatus');
$idOrder = Funciones::get('idOrder');
$one = Funciones::get('one') == '1';
$orden = Funciones::get('orden');
$esPdf = Funciones::get('pdf') == '1';
$limit = 600;

try {
	if (!$idStatus) {
		throw new FactoryExceptionCustomException('El estado es un filtro obligatorio');
	}
	$status = Factory::getInstance()->getEcommerce_OrderStatus($idStatus);
	if ($status->mostrarEnPanel == 'N') {
		throw new FactoryExceptionCustomException('El estado buscado no es visible');
	}

	$where = 'anulado = ' . Datos::objectToDB('N');
	if (!$one) {
		$where .=  ' AND cod_status = ' . Datos::objectToDB($idStatus);
		if ($fechaDesde || $fechaHasta) {
			$where .= ' AND ' . Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_pedido');
		} elseif (in_array($status->id, array(Ecommerce_OrderStatus_Finalizado::STATUS_ID))) {
			$where .= ' AND fecha_pedido > dbo.relativeDate(GETDATE(), ' . Datos::objectToDB('first') . ', -6)';
		}
	} else {
		if (!$idOrder) {
			throw new FactoryExceptionCustomException('No se pudo actualizar correctamente el registro (no se envió el número de pedido)');
		}
		$where .= ' AND (cod_order = ' . Datos::objectToDB($idOrder) . ')';
	}
	$where = trim($where, ' AND ');
	$order = ' ORDER BY ';
	switch ($orden) {
		case 1: $order .= 'fecha_pedido ASC, cod_order ASC'; break;
		case 2: $order .= 'grand_total ASC, cod_order ASC'; break;
		case 3: $order .= 'grand_total DESC, cod_order DESC'; break;
		default: $order .= 'fecha_pedido DESC, cod_order DESC'; break;
	}

	$items = Factory::getInstance()->getListObject('Ecommerce_Order', $where . $order, $limit);

	if (!$one) {
		$html = '';
		if (count($items)) {
			$arrayHeader = array(
				array('content' => 'F. pedido', 'dataType' => 'Center', 'width' => 10, 'title' => 'Fecha de pedido'),
				array('content' => 'Nº pedido EC', 'dataType' => 'Center', 'width' => 10, 'title' => 'Número de pedido de Ecommerce'),
				array('content' => 'Nº pedido Koi', 'dataType' => 'Center', 'width' => 10, 'title' => 'Número de pedido de Koi'),
				array('content' => 'Cliente', 'width' => 16),
				array('content' => 'Pares', 'dataType' => 'Center', 'width' => 7),
				array('content' => 'Total', 'dataType' => 'Moneda', 'width' => 7),
				array('content' => 'Documentos', 'dataType' => 'Center', 'width' => 32),
			);
			if(!$esPdf) {
				$arrayHeader[] = array('content' => 'Acciones', 'dataType' => 'Center', 'width' => 8);
			}

			$tabla = new HtmlTable(array('cantRows' => count($items), 'cantCols' => 7 + ($esPdf ? 0 : 1), 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%',
										'tdBaseClass' => 'pRight10 pLeft10'));
			$tabla->getRowCellArray($rows, $cells);
			$tabla->createHeaderFromArray($arrayHeader);
			$i = 0;
			foreach ($items as $item) {
				/** @var Ecommerce_Order $item */
				$cells[$i][0]->content = $item->fechaPedido;
				$cells[$i][1]->content = $item->idEcommerce;
				$cells[$i][2]->content = $item->pedido->numero;
				$cells[$i][3]->content = '[' . $item->customer->id . '] ' . $item->customer->fullname();
				$cells[$i][4]->content = $item->getCantidadPares();
				$cells[$i][5]->content = $item->grandTotal;
				$cells[$i][6]->content = '';
				foreach ($item->documents as $document) {
					$cells[$i][6]->content .= '<a href="' . $document->url . '" target="_blank">';
					$cells[$i][6]->content .= '<img class="pLeft10" src="/img/varias/' . $document->getImgName() . '.png" title="Abrir ' . $document->doctype . '" />';
					$cells[$i][6]->content .= '</a>';
				}

				if (!$esPdf) {
					$cells[$i][7]->content = '';
					if ($item->status->id == Ecommerce_OrderStatus_Finalizado::STATUS_ID) {
						$yaCambiadoDevuelto =  (!is_null($item->idCuponDeCambio)) || ($item->idServicioAndreani == Ecommerce_ServicioAndreani::RETIRO_EN_CLIENTE);
						$cells[$i][7]->content .= '<a href="#" class="boton inline-block' . ($yaCambiadoDevuelto ? '' : ' btnCambio') . '" title="Ingresar cambio" ';
						$cells[$i][7]->content .= 'data-orderid="' . $item->id . '"><img src="/img/botones/25/actualizar' . ($yaCambiadoDevuelto ? '_off' : '') . '.gif"></a>';
						$cells[$i][7]->content .= '<a href="#" class="boton inline-block' . ($yaCambiadoDevuelto ? '' : ' btnDevolucion') . '" title="Enviar a devolución" ';
						$cells[$i][7]->content .= 'data-orderid="' . $item->id . '" data-orderidecommerce="' . $item->idEcommerce . '" data-cantidadpares="' . $item->getCantidadPares() . '"><img src="/img/botones/25/rendir' . ($yaCambiadoDevuelto ? '_off' : '') . '.gif"></a>';
					}
					if ($item->status->tieneProximoStatus() && $item->status->id != Ecommerce_OrderStatus_Finalizado::STATUS_ID) {
						$cells[$i][7]->content .= '<a href="#" class="boton inline-block btnPasoSiguiente" title="Próximo paso" ';
						$cells[$i][7]->content .= 'data-orderid="' . $item->id . '" data-orderidecommerce="' . $item->idEcommerce . '" data-cantidadpares="' . $item->getCantidadPares() . '"><img src="/img/botones/25/aceptar.gif"></a>';
					}
					if ($item->status->esReversible()) {
						$cells[$i][7]->content .= '<a href="#" class="boton inline-block btnPasoAnterior" title="Paso anterior" ';
						$cells[$i][7]->content .= 'data-orderid="' . $item->id . '" data-orderidecommerce="' . $item->idEcommerce . '" data-cantidadpares="' . $item->getCantidadPares() . '"><img src="/img/botones/25/cancelar.gif"></a>';
					}
				}

				$rows[$i]->id = 'row_' . $item->id;
				$i++;
			}
			$html = $tabla->create(true);
		}

		$msg = (!count($items) ? 'No hay pedidos en este estado que cumplan con el filtro ingresado' : (count($items) == $limit ? 'Se limitó la búsqueda a ' . $limit . ' registros' : ''));
		Html::jsonEncode('', array('tabs' => tabsInfo(), 'html' => $html, 'msg' => $msg));
	} else {
		if (count($items) > 1) {
			throw new FactoryExceptionCustomException('No se pudo actualizar correctamente el registro ya que devolvió múltiples filas');
		}
		Html::jsonEncode('', array('idOrder' => $idOrder, 'idStatus' => count($items) ? $items[0]->idStatus : null));
	}
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>