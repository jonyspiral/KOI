<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/usuarios/por_seccion/borrar/')) { ?>
<?php

$idUsuario = Funciones::post('idUsuario');
$idSeccionProduccion = Funciones::post('idSeccionProduccion');

try {
	$uxa = Factory::getInstance()->getUsuarioPorSeccionProduccion($idUsuario, $idSeccionProduccion);
	$uxa->borrar()->notificar('sistema/usuarios/por_seccion/borrar/');

	Html::jsonSuccess('El usuario fue eliminado correctamente de los permisos de la sección');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('Ese usuario ya no tenía permisos sobre la sección. Por favor, recargue la página');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar eliminar el usuario de los permisos de la sección');
}
?>
<?php } ?>