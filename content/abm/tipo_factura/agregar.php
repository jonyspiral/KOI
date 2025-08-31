<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/tipo_factura/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$descripcion = Funciones::post('descripcion');

try {
	$tipo_factura = Factory::getInstance()->getTipoFactura();

	$tipo_factura->nombre = $nombre;
	$tipo_factura->descripcion = $descripcion;

	$tipo_factura->guardar()->notificar('abm/impuestos/agregar/');
	Html::jsonSuccess('El tipo de factura fue guardado correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el tipo de factura');
}

?>
<?php } ?>