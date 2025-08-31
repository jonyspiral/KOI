<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/periodos_fiscales/tipos/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$tipoPeriodoFiscal = Factory::getInstance()->getTipoPeriodoFiscal($id);
	$tipoPeriodoFiscal->borrar()->notificar('administracion/contabilidad/periodos_fiscales/tipos/borrar/');

	Html::jsonSuccess('El tipo de período fiscal fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El tipo de período fiscal que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el tipo de período fiscal');
}
?>
<?php } ?>