<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/tipo_factura/borrar/')) { ?>
<?php

$idTipoFactura = Funciones::post('idTipoFactura');

try {
	$tipoFactura = Factory::getInstance()->getTipoFactura($idTipoFactura);
	$tipoFactura->borrar()->notificar('abm/tipo_factura/borrar/');
	Html::jsonSuccess('El tipo de factura fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El tipo de factura que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el tipo de factura');
}
?>
<?php } ?>