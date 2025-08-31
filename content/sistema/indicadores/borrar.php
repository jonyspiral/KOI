<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/indicadores/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$indicador = Factory::getInstance()->getIndicador($id);
	foreach ($indicador->roles as $rol){
		Factory::getInstance()->marcarParaBorrar($rol);
	}
	$indicador->borrar()->notificar('sistema/indicadores/borrar/');
	Html::jsonSuccess('El indicador fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El indicador que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el indicador');
}
?>
<?php } ?>