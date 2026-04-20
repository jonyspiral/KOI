<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/almacenes/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$almacen = Factory::getInstance()->getAlmacen($id);
	Factory::getInstance()->marcarParaBorrar($almacen);
	Factory::getInstance()->persistir($almacen);
	Html::jsonSuccess('El almacén fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El almacén que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el almacén');
}
?>
<?php } ?>