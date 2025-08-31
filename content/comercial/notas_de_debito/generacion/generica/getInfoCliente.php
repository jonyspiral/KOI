<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_debito/generacion/generica/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$idCliente = Funciones::get('idCliente');

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