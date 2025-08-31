<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/borrar/')) { ?>
<?php
$idCliente = Funciones::post('idCliente');
try {
	$cliente = Factory::getInstance()->getClienteTodos($idCliente);
	foreach($cliente->contactos as $contacto) {
		foreach($contacto->usuarios as $usuario) {
			Factory::getInstance()->marcarParaBorrar($usuario);
			Factory::getInstance()->persistir($usuario);
		}
		Factory::getInstance()->marcarParaBorrar($contacto);
		Factory::getInstance()->persistir($contacto);
	}
	foreach($cliente->sucursales as $sucursal) {
		Factory::getInstance()->marcarParaBorrar($sucursal);
		Factory::getInstance()->persistir($sucursal);
	}
	Factory::getInstance()->marcarParaBorrar($cliente);
	Factory::getInstance()->persistir($cliente);
	Html::jsonSuccess('El cliente fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El cliente que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el cliente');
}
?>
<?php } ?>