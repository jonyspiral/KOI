<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/provincias/borrar/')) { ?>
<?php
$idPais = Funciones::post('idPais');
$idProvincia = Funciones::post('idProvincia');
try {
	$provincia = Factory::getInstance()->getProvincia($idPais, $idProvincia);
	Factory::getInstance()->marcarParaBorrar($provincia);
	Factory::getInstance()->persistir($provincia);
	Html::jsonSuccess('La provincia fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La provincia que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la provincia');
}
?>
<?php } ?>