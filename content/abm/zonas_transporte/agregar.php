<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/zonas_transporte/agregar/')) { ?>
<?php


$nombre = Funciones::post('nombre');
$descripcion = Funciones::post('descripcion');

try {
	$zonaTransporte = Factory::getInstance()->getZonaTransporte();
	
	$zonaTransporte->nombre = $nombre;
	$zonaTransporte->descripcion = $descripcion;

	Factory::getInstance()->persistir($zonaTransporte);
	Html::jsonSuccess('La zona de transporte fue guardada correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la zona de transporte');
}
?>
<?php } ?>