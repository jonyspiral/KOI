<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/localidades/borrar/')) { ?>
<?php
$idPais = Funciones::post('idPais');
$idProvincia = Funciones::post('idProvincia');
$idLocalidad = Funciones::post('idLocalidad');
try {
	$localidad = Factory::getInstance()->getLocalidad($idPais, $idProvincia, $idLocalidad);
	Factory::getInstance()->marcarParaBorrar($localidad);
	Factory::getInstance()->persistir($localidad);
	Html::jsonSuccess('La localidad fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La localidad que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la localidad');
}
?>
<?php } ?>