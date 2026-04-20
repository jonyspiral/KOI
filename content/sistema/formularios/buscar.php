<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/buscar/')) { ?>
<?php
$idCliente = Funciones::get('idCliente');
try {
	if (!isset($idCliente))
		throw new Exception();
	$cliente = Factory::getInstance()->getClienteTodos($idCliente);
	//Para el autocomplete del vendedor
	$cliente->vendedor->nombreApellido;
	//Para las autorizaciones
	$cliente->autorizaciones->expand();
	foreach($cliente->autorizaciones->autorizaciones as $key => $val){
		$cliente->autorizaciones->autorizaciones[$key]->usuario->nombre;
		$cliente->autorizaciones->autorizaciones[$key]->usuario->apellido;
	}
	$echo['cliente'] = $cliente->expand();
	$sucursales = array();
	foreach ($cliente->sucursales as $sucursal) {
		$sucursales[] = $sucursal->expand();
	}
	$echo['sucursales'] = $sucursales;
	Html::jsonEncode('', $echo);
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>