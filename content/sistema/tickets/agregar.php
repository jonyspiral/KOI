<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/tickets/agregar/')) { ?>
<?php

$idAreaEmpresa = Funciones::post('idAreaEmpresa');
$descripcion = Funciones::post('descripcion');
$prioridadExterna = Funciones::post('prioridadExterna');

try {
	$ticket = Factory::getInstance()->getKoiTicket();

	$ticket->areaEmpresa = Factory::getInstance()->getAreaEmpresa($idAreaEmpresa);
	$ticket->descripcion = $descripcion;
	$ticket->prioridadExterna = $prioridadExterna;
	$ticket->estado = KoiTicket::ESTADO_PENDIENTE;
	$ticket->usuario = Usuario::logueado();

	$ticket->guardar()->notificar('sistema/tickets/agregar/');
	Html::jsonSuccess('El ticket se agregó correctamente', array('id' => $ticket->id, 'usuario' => Usuario::logueado()->id));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar agregar el ticket');
}

?>
<?php } ?>