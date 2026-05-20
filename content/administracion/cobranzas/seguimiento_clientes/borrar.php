<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/seguimiento_clientes/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	if (!preg_match('/^[0-9]+$/', (string) $id)) {
		throw new FactoryExceptionRegistroNoExistente();
	}

	$gestionClientesCobranza = Factory::getInstance()->getSeguimientoCliente($id);
	$gestionClientesCobranza->borrar();

	Html::jsonSuccess('La gestion fue borrada correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La gestion que intento borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrio un error al intentar borrar la gestion');
}
?>
<?php } ?>
