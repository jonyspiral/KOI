<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/notificaciones/mis_notificaciones/buscar/')) { ?>
<?php

$modo = Funciones::post('modo');
$idNotificacion = Funciones::post('idNotificacion');

try {
	$nxu = Factory::getInstance()->getNotificacionPorUsuario(Usuario::logueado()->id, $idNotificacion);
	if ($modo == 'visar') {
		if ($nxu->vista != 'S') {
			$nxu->vista = 'S';
			$nxu->guardar();
		}
		Html::jsonSuccess();
	} elseif ($modo == 'eliminar') {
		if ($nxu->eliminable != 'S')
			throw new FactoryException('No se puede eliminar la notificación ya que aún no se completó la acción');
		$nxu->borrar();
		Html::jsonSuccess();
	}
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>