<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/gestion_proveedores/editar/')) { ?>
<?php

$idProveedor = Funciones::post('idProveedor');
$observaciones = Funciones::post('observaciones');

try {
	if (!isset($idProveedor)) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$proveedor = Factory::getInstance()->getProveedor($idProveedor);
	$proveedor->observacionesGestion = $observaciones;
	$proveedor->guardar()->notificar('administracion/proveedores/gestion_proveedores/editar/');

	Html::jsonSuccess('El proveedor fue editado orrectamente', $proveedor);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El proveedor que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el proveedor');
}

?>
<?php } ?>