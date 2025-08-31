<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/fajas_horarias/borrar/')) { ?>
<?php
$id= Funciones::post('id');


try {
	$horaria= Factory::getInstance()->getFajaHoraria($id);
	Factory::getInstance()->marcarParaBorrar($horaria);
	Factory::getInstance()->persistir($horaria);
	Html::jsonSuccess('La faja horaria fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La faja horaria que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la faja horaria');
} 
?>
<?php } ?>