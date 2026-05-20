<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/seguimiento_clientes/editar/')) { ?>
<?php

$id = Funciones::post('id');
$observaciones = Funciones::post('observaciones');
$estado = Funciones::post('estado');

try {
	if (!preg_match('/^[0-9]+$/', (string) $id)) {
		throw new FactoryExceptionRegistroNoExistente();
	}

	$gestionClientesCobranza = Factory::getInstance()->getSeguimientoCliente($id);

	$gestionClientesCobranza->observaciones = ($observaciones != $gestionClientesCobranza->observaciones ? $observaciones : $gestionClientesCobranza->observaciones);
	$gestionClientesCobranza->estado = $estado;

	$gestionClientesCobranza->guardar();

	Html::jsonSuccess('La gestion se edito correctamente', $gestionClientesCobranza->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('No tiene permisos para editar la gestion');
} catch (Exception $ex){
	Html::jsonError('Ocurrio un error al intentar editar la gestion Nro "' . $gestionClientesCobranza->id . '"');
}

?>
<?php } ?>
