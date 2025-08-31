<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/almacenes/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$nombreCorto = Funciones::post('nombreCorto');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();
	$almacen = Factory::getInstance()->getAlmacen($id);
	$almacen->nombre = $nombre;
	$almacen->nombreCorto = $nombreCorto;
	Factory::getInstance()->persistir($almacen);
	Html::jsonSuccess('El almacén fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El almacén que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el almcén');
}
?>
<?php } ?>