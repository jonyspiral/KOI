<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/notificaciones/tipos_de_notificaciones/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$accionNotificacion = Funciones::post('accionNotificacion');
$accionCumplido = Funciones::post('accionCumplido');
$accionAnular = Funciones::post('accionAnular');
$anularAlCumplir = Funciones::post('anularAlCumplir');
$link = Funciones::post('link');
$detalle = Funciones::post('detalle');
$imagen = Funciones::post('imagen');

try {
	$tiposNotificacion = Factory::getInstance()->getTipoNotificacion();
	$tiposNotificacion->nombre = $nombre;
	$existen = Factory::getInstance()->getListObject('TipoNotificacion', 'anulado = \'N\' AND accion_notificacion = ' . Datos::objectToDB($accionNotificacion));
	if (count($existen) > 0)
		throw new FactoryExceptionCustomException('Ya existe una notificación que se dispara con esa acción ("' . $existen[0]->nombre . '")');
	$tiposNotificacion->accionNotificacion = $accionNotificacion;
	$tiposNotificacion->accionCumplido = $accionCumplido;
	$tiposNotificacion->accionAnular = $accionAnular;
	$tiposNotificacion->anularAlCumplir = $anularAlCumplir;
	$tiposNotificacion->link = $link;
	$tiposNotificacion->detalle = $detalle;
	$tiposNotificacion->imagen = $imagen;

	Factory::getInstance()->persistir($tiposNotificacion);
	Html::jsonSuccess('El tipo de notificación fue guardado correctamente');
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el tipo de notificación');
}

?>
<?php } ?>