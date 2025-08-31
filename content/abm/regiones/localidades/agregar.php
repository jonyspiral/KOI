<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/localidades/agregar/')) { ?>
<?php

$idPais = Funciones::post('idPais');
$idProvincia = Funciones::post('idProvincia');
$nombre = Funciones::post('nombre');
$codigoPostal = Funciones::post('codigoPostal');
$idZona = Funciones::post('idZona');
try {
	$localidad = Factory::getInstance()->getLocalidad();
	$localidad->pais = Factory::getInstance()->getPais($idPais);
	$localidad->provincia = Factory::getInstance()->getProvincia($idPais, $idProvincia);
	$localidad->nombre = $nombre;
	$localidad->codigoPostal = $codigoPostal;
	$localidad->zona = Factory::getInstance()->getZona($idZona);
	Factory::getInstance()->persistir($localidad);
	Html::jsonSuccess('La localidad fue guardado correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la localidad');
}
?>
<?php } ?>