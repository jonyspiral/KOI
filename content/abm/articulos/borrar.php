<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/articulos/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$articulo = Factory::getInstance()->getArticulo($id);
	$articulo->borrar()->notificar('abm/articulos/borrar/');

	Html::jsonSuccess('El artículo fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El artículo que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el artículo');
}

?>
<?php } ?>