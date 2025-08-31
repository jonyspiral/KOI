<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/temporadas/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');

try {
	$temporada = Factory::getInstance()->getTemporada();

	$temporada->nombre = $nombre;
	$temporada->tipo = 'P';

	$temporada->guardar()->notificar('abm/temporadas/agregar/');
	Html::jsonSuccess('La temporada fue guardada correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la temporada');
}

?>
<?php } ?>