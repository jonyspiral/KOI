<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/persona_gasto/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');

try {
	$personaGasto = Factory::getInstance()->getPersonaGasto();
	$personaGasto->nombre = $nombre;
	$personaGasto->guardar()->notificar('abm/persona_gasto/agregar/');

	Html::jsonSuccess('La persona fue guardada correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la persona');
}

?>
<?php } ?>