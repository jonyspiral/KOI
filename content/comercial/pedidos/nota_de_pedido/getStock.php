<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/nota_de_pedido/')) { ?>
<?php

try {
	$stock = Stock::getStockMenosPendiente('01');
	Html::jsonEncode('', $stock);
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>