<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/tipo_factura/editar/')) { ?>
<?php

$idTipoFactura = Funciones::post('idTipoFactura');
$nombre = Funciones::post('nombre');
$descripcion = Funciones::post('descripcion');

try {
	if (!isset($idTipoFactura))
		throw new FactoryExceptionRegistroNoExistente();
	
	$tipoFactura = Factory::getInstance()->getTipoFactura($idTipoFactura);

	$tipoFactura->nombre = $nombre;
	$tipoFactura->descripcion = $descripcion;

	$tipoFactura->guardar()->notificar('abm/tipo_factura/editar/');
	Html::jsonSuccess('El tipo de factura fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El tipo de factura que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el tipo de factura');
}
?>
<?php } ?>