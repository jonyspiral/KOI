<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/paises/editar/')) { ?>
<?php

$idPais = Funciones::post('idPais');
$nombre = Funciones::post('nombre');
try {
	if (!isset($idPais))
		throw new FactoryExceptionRegistroNoExistente();
	$pais = Factory::getInstance()->getPais($idPais);
	$pais->nombre = $nombre;
	Factory::getInstance()->persistir($pais);
	Html::jsonSuccess('El país fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El país que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el país');
}
?>
<?php } ?>