<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/reimpresion/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$ordenDeCompra = Factory::getInstance()->getOrdenDeCompra($id);
	$ordenDeCompra->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>