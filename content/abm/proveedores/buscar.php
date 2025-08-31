<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/proveedores/buscar/')) { ?>
<?php
$id = Funciones::get('id');

try {
	$proveedor = Factory::getInstance()->getProveedorTodos($id);
	$proveedor->autorizaciones->expand();

	Html::jsonEncode('', $proveedor->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El proveedor "' . $id . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>