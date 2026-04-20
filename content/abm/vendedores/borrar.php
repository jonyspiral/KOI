<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/personal/borrar/')) { ?>
<?php
$id= Funciones::post('id');

try {
	$personal= Factory::getInstance()->getPersonal($id);
	Factory::getInstance()->marcarParaBorrar($personal);
	Factory::getInstance()->persistir($personal);
	Html::jsonSuccess('El personal fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El personal que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el personal');
} 
?>
<?php } ?>