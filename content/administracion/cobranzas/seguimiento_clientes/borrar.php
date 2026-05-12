<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/seguimiento_clientes/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$gestionClientesCobranza = Factory::getInstance()->getSeguimientoCliente($id);
	$gestionClientesCobranza->borrar();

	Html::jsonSuccess('La gestión fue borrado correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La gestión que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la gestión');
}
?>
<?php } ?>