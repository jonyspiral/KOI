<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/usuarios/por_almacen/borrar/')) { ?>
<?php

$idUsuario = Funciones::post('idUsuario');
$idAlmacen = Funciones::post('idAlmacen');

try {
	$uxa = Factory::getInstance()->getUsuarioPorAlmacen($idUsuario, $idAlmacen);
	$uxa->borrar()->notificar('sistema/usuarios/por_almacen/borrar/');

	Html::jsonSuccess('El usuario fue eliminado correctamente de los permisos del almacén');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('Ese usuario ya no tenía permisos sobre el almacén. Por favor, recargue la página');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar eliminar el usuario de los permisos del almacén');
}
?>
<?php } ?>