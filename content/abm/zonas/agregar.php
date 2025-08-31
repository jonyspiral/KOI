<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/zonas/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$descripcion = Funciones::post('descripcion');

try {
	$zona = Factory::getInstance()->getZona();
	$zona->nombre = $nombre;
	$zona->descripcion = $descripcion;
	$zona->guardar()->notificar('abm/zonas/agregar/');
	Html::jsonSuccess('La zona fue guardada correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la zona');
}

?>
<?php } ?>