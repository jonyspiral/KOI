<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/temporadas/editar/')) { ?>
<?php

$idTemporada = Funciones::post('idTemporada');
$nombre = Funciones::post('nombre');

try {
	if (!isset($idTemporada))
		throw new FactoryExceptionRegistroNoExistente();
	
	$impuesto = Factory::getInstance()->getTemporada($idTemporada);

	$impuesto->nombre = $nombre;
	$impuesto->tipo = 'P';

	$impuesto->guardar()->notificar('abm/temporadas/editar/');
	Html::jsonSuccess('La temporada fue editada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La temporada que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar la temporada');
}
?>
<?php } ?>