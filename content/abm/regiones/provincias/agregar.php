<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/provincias/agregar/')) { ?>
<?php

$idPais = Funciones::post('idPais');
$idProvincia = Funciones::post('idProvincia');
$nombre = Funciones::post('nombre');
try {
	if (!isset($idPais))
		throw new FactoryExceptionRegistroNoExistente();
	$provincia = Factory::getInstance()->getProvincia();
	$provincia->id = $idProvincia;
	$provincia->pais = Factory::getInstance()->getPais($idPais);
	$provincia->nombre = $nombre;
	Factory::getInstance()->persistir($provincia);
	Html::jsonSuccess('La provincia fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $ex){
	Html::jsonError('Debe ingresar un código para la provincia');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la provincia');
}
?>
<?php } ?>