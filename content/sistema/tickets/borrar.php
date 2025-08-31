<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/tickets/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$ticket = Factory::getInstance()->getKoiTicket($id);
	if (!$ticket->esAutor(Usuario::logueado())) {
		throw new FactoryExceptionCustomException('Sólo el autor puede eliminar su ticket');
	}
	$ticket->borrar()->notificar('sistema/tickets/borrar/');
	Html::jsonSuccess('El ticket fue borrado correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El ticket que intentó borrar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar borrar el ticket');
}

?>
<?php } ?>