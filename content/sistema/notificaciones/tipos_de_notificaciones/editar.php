<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/notificaciones/tipos_de_notificaciones/editar/')) { ?>
<?php
$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$accionNotificacion = Funciones::post('accionNotificacion');
$accionCumplido = Funciones::post('accionCumplido');
$accionAnular = Funciones::post('accionAnular');
$anularAlCumplir = Funciones::post('anularAlCumplir');
$link = Funciones::post('link');
$detalle = Funciones::post('detalle');
$imagen = Funciones::post('imagen');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();
	$tiposNotificacion = Factory::getInstance()->getTipoNotificacion($id);
	$tiposNotificacion->nombre = $nombre;
	$existen = Factory::getInstance()->getListObject('TipoNotificacion', 'anulado = \'N\' AND accion_notificacion = ' . Datos::objectToDB($accionNotificacion));
	if (count($existen) > 0 && $existen[0]->id != $tiposNotificacion->id) {
		throw new FactoryExceptionCustomException('Ya existe una notificación que se dispara con esa acción ("' . $existen[0]->nombre . '")');
	}
	$tiposNotificacion->accionNotificacion = $accionNotificacion;
	$tiposNotificacion->accionCumplido = $accionCumplido;
	$tiposNotificacion->accionAnular = $accionAnular;
	$tiposNotificacion->anularAlCumplir = $anularAlCumplir;
	$tiposNotificacion->link = $link;
	$tiposNotificacion->detalle = $detalle;
	$tiposNotificacion->imagen = $imagen;

	Factory::getInstance()->persistir($tiposNotificacion);
	Html::jsonSuccess('El tipo de notificación fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El tipo de notificación que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el tipo de notificación');
}
?>
<?php } ?>