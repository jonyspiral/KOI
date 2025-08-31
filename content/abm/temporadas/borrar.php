<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/temporadas/borrar/')) { ?>
<?php

$idTemporada = Funciones::post('idTemporada');

try {
	$temporada = Factory::getInstance()->getTemporada($idTemporada);
	$temporada->borrar()->notificar('abm/temporadas/borrar/');
	Html::jsonSuccess('La temporada fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La temporada que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la temporada');
}
?>
<?php } ?>