<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/ecommerce/panel_de_control/agregar/')) { ?>
<?php

$orderId = Funciones::post('orderId');

try {
	$order = Factory::getInstance()->getEcommerce_Order($orderId);
	$oldStatus = $order->idStatus;
	$order->avanzarStatus();

	Html::jsonSuccess('Se movió correctamente el pedido al próximo estado', array('orderId' => $order->id, 'statusId' => ($order->status->mostrarEnPanel == 'N' ? $oldStatus : $order->status->id)));
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar mover el pedido al próximo estado');
}
?>
<?php } ?>