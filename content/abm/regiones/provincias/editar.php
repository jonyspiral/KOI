<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/provincias/editar/')) { ?>
<?php

$idPais = Funciones::post('idPais');
$idProvincia = Funciones::post('idProvincia');
$nombre = Funciones::post('nombre');
try {
	if (!isset($idProvincia))
		throw new FactoryExceptionRegistroNoExistente();
	$provincia = Factory::getInstance()->getProvincia($idPais, $idProvincia);
	$provincia->nombre = $nombre;
	Factory::getInstance()->persistir($provincia);
	Html::jsonSuccess('La provincia fue guardada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La provincia que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la provincia');
}
?>
<?php } ?>