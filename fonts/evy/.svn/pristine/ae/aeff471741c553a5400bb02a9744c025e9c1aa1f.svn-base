<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/curvas/borrar/')) { ?>
<?php
$id= Funciones::post('id');


try {
	$curvas= Factory::getInstance()->getCurva($id);
	Factory::getInstance()->marcarParaBorrar($curvas);
	Factory::getInstance()->persistir($curvas);
	Html::jsonSuccess('La curva fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La curva que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la curva');
} 
?>
<?php } ?>