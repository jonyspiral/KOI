<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('cliente/pedidos/')) { ?>
<?php

$id = Funciones::get('id');

try {
    $pedido = PedidoCliente::find($id);
    $pedido->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>