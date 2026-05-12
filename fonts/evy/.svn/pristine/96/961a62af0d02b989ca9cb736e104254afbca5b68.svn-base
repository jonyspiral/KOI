<?php
require_once('premaster.php');

$heartbeat = Funciones::post('heartbeat');

try {
	if (Usuario::logueado()) {
		Usuario::logueado()->heartbeat();
		Usuario::logueado()->anularNotificaciones(Funciones::keyIsSet($heartbeat, 'anuladas', array()));
		Usuario::logueado()->visarNotificaciones(Funciones::keyIsSet($heartbeat, 'vistas', array()));
		$notificaciones = Usuario::logueado()->getNotificaciones($heartbeat['ultimaFechaHora']);
		Html::jsonEncode('', $notificaciones);
	} else {
		throw new FactoryExceptionCustomException('Se ha perdido la conexión con el servidor');
	}
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonAlert($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>