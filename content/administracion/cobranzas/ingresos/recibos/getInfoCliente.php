<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/recibos/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');

try {
	$cliente = Factory::getInstance()->getCliente($idCliente);
	Html::jsonEncode('', array('cuit' => $cliente->cuit, 'nombre' => $cliente->razonSocial));
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>