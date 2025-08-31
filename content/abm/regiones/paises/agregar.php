<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('abm/regiones/paises/agregar/')) { ?>
<?php

$idPais = Funciones::post('idPais');
$nombre = Funciones::post('nombre');
try {
	if (!isset($idPais))
		throw new FactoryExceptionRegistroNoExistente();
	$pais = Factory::getInstance()->getPais();
	$pais->id = $idPais;
	$pais->nombre = $nombre;
	Factory::getInstance()->persistir($pais);
	Html::jsonSuccess('El país fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $ex){
	Html::jsonError('Debe ingresar un código para el país');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el país');
}
?>
<?php } ?>