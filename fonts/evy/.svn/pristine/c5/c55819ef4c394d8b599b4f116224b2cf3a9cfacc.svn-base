<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/zonas/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$descripcion = Funciones::post('descripcion');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();
	$zona = Factory::getInstance()->getZona($id);
	$zona->nombre = $nombre;
	$zona->descripcion = $descripcion;
	$zona->guardar()->notificar('abm/zonas/editar/');
	Html::jsonSuccess('La zona fue guardada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La zona que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la zona');
}
?>
<?php } ?>