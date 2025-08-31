<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/ecommerce/panel_de_control/editar/')) { ?>
<?php

$orderId = Funciones::post('orderId');

try {
	$order = Factory::getInstance()->getEcommerce_Order($orderId);
	$oldStatus = $order->idStatus;
	$newStatus = Ecommerce_OrderStatus::forge(Ecommerce_OrderStatus_PendienteDeDevolucion::STATUS_ID);
	$order->avanzarStatus($newStatus);

	Html::jsonSuccess('Se movió correctamente el pedido al estado de devolución', array('orderId' => $order->id, 'statusId' => ($order->status->mostrarEnPanel == 'N' ? $oldStatus : $order->status->id)));
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar mover el pedido al estado de devolución');
}

?>
<?php } ?>