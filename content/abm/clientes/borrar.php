<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/borrar/')) { ?>
<?php

$idCliente = Funciones::post('idCliente');

try {
	Factory::getInstance()->beginTransaction();

	$cliente = Factory::getInstance()->getClienteTodos($idCliente);

	foreach($cliente->contactos as $contacto) {
		foreach($contacto->usuarios as $usuario) {
			$usuario->borrar()->notificar('sistema/usuarios/abm/borrar/');
		}
		$contacto->borrar()->notificar('abm/contactos/borrar/');
	}
	foreach($cliente->sucursales as $sucursal) {
		$sucursal->borrar()->notificar('abm/sucursales/borrar/');
	}
	$cliente->borrar()->notificar('abm/clientes/borrar/');

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('El cliente fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('El cliente que intentó borrar no existe');
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar borrar el cliente');
}
?>
<?php } ?>