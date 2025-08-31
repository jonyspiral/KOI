<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/ecommerce/reporte_ventas/buscar/')) { ?>
<?php

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$customer = Funciones::get('customer');
$usergroup = Funciones::get('usergroup');
$modo = Funciones::get('modo'); //Detalle o acumulado

try {
	$sql = Funciones::strFechas($desde, $hasta, 'fecha_pedido') . ' AND 1 = 1 ';
	isset($customer) && $sql .= ' AND cod_customer = ' . Datos::objectToDB($customer);
	isset($usergroup) && $sql .= ' AND cod_usergroup = ' . Datos::objectToDB($usergroup);
	$sql = trim($sql, ' AND ');
	$fields = '*';
	if ($modo == '1') {
		$fields = array('cod_customer', 'firstname', 'lastname', 'cod_usergroup', 'nombre_usergroup', 'SUM(total_discount) total_discount', 'SUM(grand_total) grand_total');
		$sql .= ' GROUP BY cod_customer, firstname, lastname, cod_usergroup, nombre_usergroup';
	}
	$sql .= ' ORDER BY ' . ($modo == '1' ? 'cod_customer ASC' : 'fecha_pedido ASC');
	$orders = Factory::getInstance()->getArrayFromView('ecommerce_orders_v', $sql, 0, $fields);
	if (!count($orders)) {
		throw new FactoryExceptionCustomException('No hay pedidos con ese filtro');
	}

	$totalizada = $modo == '1';
	$tabla = new HtmlTable(array('cantRows' => count($orders), 'cantCols' => ($totalizada ? 4 : 5), 'class' => 'pBottom10', 'cellSpacing' => 1, 'width' => '100%',
								 'tdBaseClass' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray', 'tdBaseClassLast' => 'pRight10 pLeft10 bBottomDarkGray bLeftDarkGray bRightDarkGray'));
	if ($totalizada) {
		$tabla->createHeaderFromArray(
			  array(
				  array('content' => 'Grupo', 'width' => 30),
				  array('content' => 'Cliente', 'width' => 30),
				  array('content' => 'Descuentos', 'dataType' => 'Moneda', 'width' => 20),
				  array('content' => 'Total', 'dataType' => 'Moneda', 'width' => 20)
			  )
		);
	} else {
		$tabla->createHeaderFromArray(
			  array(
				  array('content' => 'Fecha', 'dataType' => 'Fecha', 'width' => 20),
				  array('content' => 'Grupo', 'width' => 20),
				  array('content' => 'Cliente', 'width' => 30),
				  array('content' => 'Descuentos', 'dataType' => 'Moneda', 'width' => 15),
				  array('content' => 'Total', 'dataType' => 'Moneda', 'width' => 15)
			  )
		);
	}
	$tabla->getRowCellArray($rows, $cells);
	for ($i = 0; $i < $tabla->cantRows; $i++) {
		/** @var Documento $doc */
		$order = $orders[$i];
		$j = ($totalizada ? 0 : 1);

		(!$totalizada) && $cells[$i][0]->content = $order['fecha_pedido'];
		$cells[$i][0 + $j]->content = $order['nombre_usergroup'];
		$cells[$i][1 + $j]->content = $order['firstname'] . ' ' . $order['lastname'];
		$cells[$i][2 + $j]->content = $order['total_discount'];
		$cells[$i][3 + $j]->content = $order['grand_total'];
	}
	$tabla->create();

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>