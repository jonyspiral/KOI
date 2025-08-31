<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/persona_gasto/borrar/')) { ?>
<?php

$idPersonaGasto = Funciones::post('idPersonaGasto');

try {
	$personaGasto = Factory::getInstance()->getPersonaGasto($idPersonaGasto);
	$personaGasto->borrar()->notificar('abm/persona_gasto/borrar/');

	Html::jsonSuccess('La persona fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La persona que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la persona');
}

?>
<?php } ?>