<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/provincias/buscar/')) { ?>
<?php
$idPais = Funciones::get('idPais');
$idProvincia = Funciones::get('idProvincia');
try {
	$provincia = Factory::getInstance()->getProvincia($idPais, $idProvincia);
	Html::jsonEncode('', $provincia->expand());

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La provincia "' . $idProvincia . '" no existe o no tiene permiso para visualizarla');
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>