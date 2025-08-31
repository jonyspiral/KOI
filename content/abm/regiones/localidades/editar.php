<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/localidades/editar/')) { ?>
<?php

$idPais = Funciones::post('idPais');
$idProvincia = Funciones::post('idProvincia');
$idLocalidad = Funciones::post('idLocalidad');
$nombre = Funciones::post('nombre');
$codigoPostal = Funciones::post('codigoPostal');
$idZona = Funciones::post('idZona');
try {
	if (!isset($idLocalidad))
		throw new FactoryExceptionRegistroNoExistente();
	$localidad = Factory::getInstance()->getLocalidad($idPais, $idProvincia, $idLocalidad);
	$localidad->nombre = $nombre;
	$localidad->codigoPostal = $codigoPostal;
	$localidad->zona = Factory::getInstance()->getZona($idZona);
	Factory::getInstance()->persistir($localidad);
	Html::jsonSuccess('La localidad fue guardada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La localidad que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la localidad');
}
?>
<?php } ?>