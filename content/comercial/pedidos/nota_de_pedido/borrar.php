<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/nota_de_pedido/borrar/')) { ?>
<?php

$idNotaDePedido = Funciones::post('idNotaDePedido');

try {
    Factory::getInstance()->beginTransaction();

	$notaDePedido = Factory::getInstance()->getPedido($idNotaDePedido);
	$notaDePedido->borrar()->notificar('comercial/pedidos/nota_de_pedido/borrar/');

    // Si viene de un PedidoCliente, lo borramos
    $pedidosCliente = Base::getListObject('PedidoCliente', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_pedido = ' . $notaDePedido->numero);
    if (count($pedidosCliente) == 1) {
        $pedidoCliente = $pedidosCliente[0];
        $pedidoCliente->borrar();
    }

    Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('La nota de pedido fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
    Factory::getInstance()->rollbackTransaction();
	Html::jsonError('La nota de pedido que intentó borrar no existe');
} catch (Exception $ex){
    Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar borrar la nota de pedido');
}

?>
<?php } ?>