<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/ecommerce/panel_de_control/buscar/')) { ?>
<?php

function idCliente(Ecommerce_Order $order) {
	//SPIRAL14000001
	return 'SPIRAL' . Funciones::formatearFecha($item->fechaPedido, 'YY') . Funciones::padLeft($order->idEcommerce, 6);
}

$orderId = Funciones::get('orderId');

try {
	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= $orderId ? ('cod_order = ' . Datos::objectToDB($orderId)) : ('cod_status = ' . Datos::objectToDB(Ecommerce_OrderStatus_Remitido::STATUS_ID));
	$order = ' ORDER BY fecha_pedido ASC, cod_order ASC';
	$items = Factory::getInstance()->getListObject('Ecommerce_Order', $where . $order);

	$array = array();
	foreach ($items as $item) {
		/** @var Ecommerce_Order $item */
		if ($item->customer->usergroup->empresa == 1) {
			$array[] = array(
				idCliente($item),
				$item->delivery->receptorName,				// Destinatario
				'',											// Titular alternativo
				$item->delivery->street,					// Calle
				'',											// Piso
				'',											// Puerta
				'',											// Departamento
				$item->delivery->pbox,						// Código Postal
				$item->delivery->city,						// Ciudad (Alfanumérico - Longitud 50)
				//$item->delivery->province,				// Provincia (Alfanumérico - Longitud 50)
				//$item->delivery->country,					// Pais (Alfanumérico - Longitud 50)
				'CL0009907',								// Cuenta Corriente
				$item->servicioAndreani->numeroDeContrato,	// Servicio: Código de contrato con Andreani que indica el tipo de servicio:
															// 		400004562    --> Envío con Entrega en domicilio Urgente
															// 		400004710    --> Envío de un producto de cambio
															// 		400004711    --> Retiro de producto en domicilio del cliente
				'',											// Referencia 2
				'',											// Referencia 3
				'',											// Referencia 4
				//$item->delivery->phone,					// Teléfono (Alfanumérico - Longitud 50)
				//$item->delivery->expectedDate,			// Fecha de entrega - opcional (Alfanumérico - Longitud 20 - Formato DD-MM-AAAA HH:MI:SS)
				//$item->delivery->timeFrame				// Hora de entrega - opcional (Alfanumérico - Longitud 50)
			);
		}
	}

	$array2csv = new Array2Csv();
	$array2csv->loadArray($array);;
	$array2csv->fileName = 'Andreani_CSV_' . time();
	$array2csv->download();
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonInfo('No hay ningún pedido para generar el archivo CSV');
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>