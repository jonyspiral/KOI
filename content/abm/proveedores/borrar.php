<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/proveedores/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$proveedor = Factory::getInstance()->getProveedorTodos($id);
	$proveedor->borrar()->notificar('abm/proveedores/borrar/');

	Html::jsonSuccess('El proveedor fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El proveedor que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el proveedor');
} 
?>
<?php } ?>