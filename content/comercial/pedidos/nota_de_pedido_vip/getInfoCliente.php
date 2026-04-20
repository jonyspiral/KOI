<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/nota_de_pedido_vip/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
try {
	$cliente = Factory::getInstance()->getClienteTodos($idCliente);
	Html::jsonEncode('', array(
		'vendedor' => $cliente->vendedor->expand(),
		'listaAplicable' => $cliente->listaAplicable
	));
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>