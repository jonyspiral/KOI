<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/persona_gasto/editar/')) { ?>
<?php

$idPersonaGasto = Funciones::post('idPersonaGasto');
$nombre = Funciones::post('nombre');

try {
	if (!isset($idPersonaGasto)) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$personaGasto = Factory::getInstance()->getPersonaGasto($idPersonaGasto);
	$personaGasto->nombre = $nombre;
	$personaGasto->guardar()->notificar('abm/persona_gasto/editar/');

	Html::jsonSuccess('La persona fue guardada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La persona que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar la persona');
}
?>
<?php } ?>