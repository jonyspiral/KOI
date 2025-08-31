<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/buscar/')) { ?>
<?php
$idCliente = Funciones::get('idCliente');
try {
	if (!isset($idCliente))
		throw new Exception();
	$cliente = Factory::getInstance()->getClienteTodos($idCliente);

	if (Usuario::logueado()->esVendedor()) {
		$vendedor = Factory::getInstance()->getVendedor(Usuario::logueado()->personal->id);
		if ($cliente->vendedor->id != $vendedor->id) {
			throw new FactoryExceptionCustomException('El cliente "' . $cliente->id . '" no existe o no tiene permiso para visualizarlo.');
		}
	}

	//Para el autocomplete del vendedor
	$cliente->vendedor->nombreApellido;
	//Para las autorizaciones
	$cliente->autorizaciones->expand();
	$echo['cliente'] = $cliente->expand();
	$sucursales = array();
	foreach ($cliente->sucursales as $sucursal) {
		$sucursales[] = $sucursal->expand();
	}
	$echo['sucursales'] = $sucursales;
	$echo['entregarSucEntrega'] = ($cliente->tieneSucursalEntrega() ? 'S' : 'N');

	Html::jsonEncode('', $echo);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El cliente "' . $idCliente . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>