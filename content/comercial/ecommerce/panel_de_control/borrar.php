<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/ecommerce/panel_de_control/borrar/')) { ?>
<?php

$orderId = Funciones::post('orderId');

try {
	$order = Factory::getInstance()->getEcommerce_Order($orderId);
	$oldStatus = $order->idStatus;
	$order->retrocederStatus();

	Html::jsonSuccess('Se volvió correctamente el pedido al estado anterior', array('orderId' => $order->id, 'statusId' => ($order->status->mostrarEnPanel == 'N' ? $oldStatus : $order->status->id)));
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar volver el pedido al estado anterior');
}
?>
<?php } ?>