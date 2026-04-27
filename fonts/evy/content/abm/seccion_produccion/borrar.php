<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/seccion_produccion/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$seccion = Factory::getInstance()->getSeccionProduccion($id);
	$seccion->borrar()->notificar('abm/seccion_produccion/borrar/');

	Html::jsonSuccess('La sección fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La sección que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la sección');
}

?>
<?php } ?>