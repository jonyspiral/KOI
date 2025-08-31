<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/calidad/devoluciones_a_clientes/buscar/')) { ?>
<?php

$idDevolucion = Funciones::get('idDevolucion');

try {
	$devolucionACliente = Factory::getInstance()->getDevolucionACliente($idDevolucion);
	$devolucionACliente->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>