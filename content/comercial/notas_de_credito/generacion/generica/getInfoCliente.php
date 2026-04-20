<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/generica/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$empresa = Funciones::session('empresa');

try {
	$cliente = Factory::getInstance()->getClienteTodos($idCliente);
	Html::jsonEncode('', array(
		'descuento' => $cliente->creditoDescuentoEspecial,
		'ivaPorc' => ($empresa == 2 ? 0 : $cliente->condicionIva->porcentajes[1])
	));
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>