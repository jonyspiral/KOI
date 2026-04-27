<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/ecommerce/panel_de_control/editar/')) { ?>
<?php

$orderId = Funciones::post('orderId');
$detalles = Funciones::post('detalles');
//$nroCupon = Funciones::post('nroCupon');

try {
	$order = Factory::getInstance()->getEcommerce_Order($orderId);
	$total = 0;
	$pares = 0;
	foreach ($order->details as $detail) {
		if (isset($detalles[$detail->id])) {
			if ($detalles[$detail->id] > $detail->quantity) {
				throw new FactoryExceptionCustomException('No se puede ingresar una cantidad para cambiar mayor que la que fue pedida');
			}
			$total += $detail->price * $detalles[$detail->id];
			$pares += $detalles[$detail->id];
		}
	}
	if ($pares <= 0) {
		throw new FactoryExceptionCustomException('No se puede realizar un cambio sin pares');
	}

	//Cambio el status
	$oldStatus = $order->idStatus;
	$newStatus = Ecommerce_OrderStatus::forge(Ecommerce_OrderStatus_PendienteDeCambio::STATUS_ID);
	$order->cuponDeCambioImporte = $total;

	$ws = new Ecommerce_WS();
	$coupon = array(
		'partialuse'	=> 'true',
		'customerid'	=> $order->customer->idEcommerce,
		'validfrom'		=> Funciones::formatearFecha(Funciones::hoy(), 'd-m-Y H:i:s'),
		'validto'		=> Funciones::sumarTiempo(Funciones::hoy(), 6, 'month', 'd-m-Y'),
		'amount'		=> Funciones::formatearDecimales($total, 2, '.')
	);
	$coupon = $ws->create_coupons(
		array(
			'sessionid'	=> md5($coupon['validfrom'] . $coupon['customerid'] . $coupon['amount'] . $coupon['validto']),
			'coupon'	=> $coupon
		)
	);

	$order->idCuponDeCambio = $coupon['couponcode'];
	$order->avanzarStatus($newStatus);

	Html::jsonSuccess('Se generó correctamente el cupón de descuento y se movió el pedido al estado de cambio', array('orderId' => $order->id, 'statusId' => ($order->status->mostrarEnPanel == 'N' ? $oldStatus : $order->status->id)));
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError(ucfirst($ex->getMessage()));
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el cupón de descuento y mover el pedido al estado de cambio: <br>' . $ex->getMessage());
}

?>
<?php } ?>