<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/notificaciones/mis_notificaciones/buscar/')) { ?>
<?php

$busqueda = Funciones::post('busqueda');

function getImgPath($name) {
	$file = '/img/notificaciones/' . $name;
	return (file_exists(Config::pathBase . $file) ? $file : '');
}

function getLink($link) {
	return ($link != '' ? Config::urlBase . $link : '#');
}

function encodeNotificaciones($notificaciones) {
	$arr = array();
	foreach($notificaciones as $not) {
		$arr[] = array(
			'idNotificacion'		=> $not->idNotificacion,
			'detalle'				=> $not->notificacion->detalle,
			'fecha'					=> $not->notificacion->fechaAlta,
			'link'					=> getLink($not->notificacion->link),
			'anulado'				=> $not->anulado,
			'vista'					=> $not->vista,
			'eliminable'			=> $not->eliminable,
			'idTipoNotificacion'	=> $not->notificacion->idTipoNotificacion
		);
	}
	return $arr;
}

function encodeTipos($tipos) {
	$arr = array();
	foreach($tipos as $tipo) {
		$arr[$tipo->id] = array(
			'id'		=> $tipo->id,
			'nombre'	=> $tipo->nombre,
			'imagen'	=> getImgPath($tipo->imagen),
			'tildado'	=> '' //Se setea como TRUE o FALSE en JS
		);
	}
	return $arr;
}

try {
	if ($busqueda == 1) {
		Html::jsonEncode('', encodeTipos(Factory::getInstance()->getListObject('TipoNotificacion', 'anulado = \'N\' ')));
	} else {
		$where = 'anulado = \'N\' AND cod_usuario = ' . Datos::objectToDB(Usuario::logueado()->id);
		Html::jsonEncode('', encodeNotificaciones(Factory::getInstance()->getListObject('NotificacionPorUsuario', $where)));
	}
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>