<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/tickets/editar/')) { ?>
<?php

$idTicket = Funciones::post('id');
$descripcion = Funciones::post('descripcion');
$prioridadExterna = Funciones::post('prioridadExterna');
$prioridadInterna = Funciones::post('prioridadInterna');
$idResponsable = Funciones::post('idResponsable');
$fechaEstimadaResolucion = Funciones::formatearFecha(Funciones::post('fechaEstimadaResolucion'));
$respuesta = Funciones::post('respuesta');
$estado = Funciones::post('estado');
$auxValue = Funciones::post('auxValue');

try {
	$ticket = Factory::getInstance()->getKoiTicket($idTicket);
	if ($ticket->fechaCierre) {
		throw new FactoryExceptionCustomException('No se puede editar un ticket cerrado');
	}

	//Campos editables por parte del autor
	if ($ticket->usuario->id == Usuario::logueado()->id) {
		$ticket->descripcion = $descripcion;
		$ticket->prioridadExterna = $prioridadExterna;
	}

	if (KoiTicket::usuarioEsResponsableDelArea(Usuario::logueado()->id, $ticket->areaEmpresa->id)) {
		//Campos editables por parte de los responsables
		$ticket->prioridadInterna = $prioridadInterna;
		$ticket->responsable = Factory::getInstance()->getUsuario($idResponsable);
		$ticket->fechaEstimadaResolucion = $fechaEstimadaResolucion;
		$ticket->respuesta = $respuesta;

		//Casos en los que se cierra el ticket
		if (in_array($estado, array(KoiTicket::ESTADO_RESUELTO, KoiTicket::ESTADO_RECHAZADO, KoiTicket::ESTADO_DELEGADO))) {
			$ticket->usuarioCierre = Usuario::logueado();
			$ticket->fechaCierre = Funciones::hoy();
			if (is_null($ticket->responsable->id)) {
				$ticket->responsable = Usuario::logueado();
			}
			if (is_null($ticket->fechaEstimadaResolucion)) {
				$ticket->fechaEstimadaResolucion = $ticket->fechaCierre;
			}
			if ($estado == KoiTicket::ESTADO_RESUELTO) {
				$ticket->resolver('sistema/tickets/editar/');
			} elseif ($estado == KoiTicket::ESTADO_RECHAZADO) {
				$ticket->rechazar('sistema/tickets/editar/', $auxValue);
			} elseif ($estado == KoiTicket::ESTADO_DELEGADO) {
				$ticket->delegar('sistema/tickets/editar/', $auxValue);
			}
		} else {
			$ticket->guardar()->notificar('sistema/tickets/editar/');
		}
	} else {
		$ticket->guardar()->notificar('sistema/tickets/editar/');
	}

	Html::jsonSuccess('El ticket se editó correctamente', array('id' => $gastito->id, 'fechaAlta' => Funciones::hoy()));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('No tine permisos para editar este ticket');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar editar el ticket "' . $ticket->id . '"');
}

?>
<?php } ?>