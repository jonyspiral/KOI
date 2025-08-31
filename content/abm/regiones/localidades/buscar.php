<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/localidades/buscar/')) { ?>
<?php
$idPais = Funciones::get('idPais');
$idProvincia = Funciones::get('idProvincia');
$idLocalidad = Funciones::get('idLocalidad');
try {
	$localidad = Factory::getInstance()->getLocalidad($idPais, $idProvincia, $idLocalidad);
	Html::jsonEncode('', $localidad->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La localidad "' . $idLocalidad . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>