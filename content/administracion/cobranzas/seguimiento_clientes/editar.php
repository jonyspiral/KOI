<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/seguimiento_clientes/editar/')) { ?>
<?php

$id = Funciones::post('id');
$observaciones = Funciones::post('observaciones');
$estado = Funciones::post('estado');

try {
	$gestionClientesCobranza = Factory::getInstance()->getSeguimientoCliente($id);

	$gestionClientesCobranza->observaciones = ($observaciones != $gestionClientesCobranza->observaciones ? $observaciones : $gestionClientesCobranza->observaciones);
	$gestionClientesCobranza->estado = $estado;

	$gestionClientesCobranza->guardar();

	Html::jsonSuccess('La gestión se editó correctamente', $gestionClientesCobranza->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('No tine permisos para editar la gestión');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar la gestión Nº "' . $gestionClientesCobranza->id . '"');
}

?>
<?php } ?>