<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/zonas_transporte/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$descripcion = Funciones::post('descripcion');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();
	$zonaTransporte = Factory::getInstance()->getZonaTransporte($id);
	
	$zonaTransporte->nombre = $nombre;
	$zonaTransporte->descripcion = $descripcion;

	Factory::getInstance()->persistir($zonaTransporte);
	Html::jsonSuccess('La zona de transporte fue guardada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La zona de transporte que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la zona de transporte');
}
?>
<?php } ?>