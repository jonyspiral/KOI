<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('cliente/pedidos/borrar/')) { ?>
<?php

$id = $_POST['id'];

try {
    Factory::getInstance()->beginTransaction();

    $pedido = PedidoCliente::find($id);
    $pedido->borrar();

    if ($pedido->idPedido && !$pedido->pedido->anulado() && $pedido->pedido->aprobado == 'N') {
        $pedido->pedido->borrar();
    }

    Factory::getInstance()->commitTransaction();

    Html::jsonSuccess('El pedidos fue eliminado correctamente');
} catch (Exception $ex) {
    Factory::getInstance()->rollbackTransaction();
    Logger::addError($ex->getMessage());
    Html::jsonError('Ocurrió un error al intentar eliminar el pedido');
}

?>
<?php } ?>

