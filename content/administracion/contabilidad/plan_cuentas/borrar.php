<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/plan_cuentas/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$imputacion = Factory::getInstance()->getImputacion($id);
	Factory::getInstance()->marcarParaBorrar($imputacion);
	$imputacion->borrar()->notificar('abm/imputaciones/borrar/');
	Html::jsonSuccess('La imputación fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La imputación que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la imputación');
}
?>
<?php } ?>