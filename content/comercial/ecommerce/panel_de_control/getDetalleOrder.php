<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/ecommerce/panel_de_control/buscar/')) { ?>
<?php

$idOrder = Funciones::get('orderId');

try {
	if (!$idOrder) {
		throw new FactoryExceptionCustomException('El filtro ID de Order es obligatorio');
	}
	$order = Factory::getInstance()->getEcommerce_Order($idOrder);

	$html = '<div class="p10"><label class="bold">Seleccionar pares a cambiar</label></div>';

	if (count($order->details)) {
		$tabla = new HtmlTable(array('cantRows' => (count($order->details)), 'cantCols' => 5, 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%',
									 'tdBaseClass' => 'pRight10 pLeft10'));
		$tabla->createHeaderFromArray(
			  array(
				  array('content' => 'Artículo', 'title' => 'Descripción del artículo'),
				  array('content' => 'Cant. compra', 'dataType' => 'Center', 'title' => 'Cantidad comprada del artículo'),
				  array('content' => 'Precio unitario', 'dataType' => 'Moneda', 'title' => 'Precio unitario del artículo'),
				  array('content' => 'Cant. cambio', 'title' => 'Cantidad de pares a cambiar'),
				  array('content' => 'Subtotal cupón', 'dataType' => 'Moneda', 'title' => 'Subtotal del cupón')
			  )
		);

		$tabla->getRowCellArray($rows, $cells);
		$i = 0;
		foreach ($order->details as $item) {
			$cells[$i][0]->content = $item->description;
			$cells[$i][1]->content = $item->quantity;
			$cells[$i][2]->content = $item->price;
			$cells[$i][3]->content = '<input type="number" class="textbox w80p aCenter inputCantidadCambiar" data-price="' . $item->price . '" data-orderid="' . $item->idOrder. '" data-detailid="' . $item->id . '" min="0" max="' . $item->quantity . '" validate="EnteroPositivo" />';
			$cells[$i][4]->content = '0';
			$cells[$i][4]->id = 'subtotal_' . $item->id;
			$i++;
		}

		$tabla->getFootArray($foots);
		$tabla->foot->tdBaseClass = 'bold s16 p5 bTopGray bBottomGray aCenter';
		$foots[0]->content = 'TOTALES';
		$foots[0]->colspan = 3;
		$foots[3]->content = '0';
		$foots[3]->id = 'totalCantidad';
		$foots[4]->content = '0';
		$foots[4]->id = 'totalSubtotal';

		$html .= $tabla->create(true);
	}

	$msg = (!count($order->details) ? 'No hay detalles en este pedido' : '');
	Html::jsonEncode('', array('html' => $html, 'msg' => $msg));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>