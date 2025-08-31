<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cajas/transferencia_interna/agregar/')) { ?>
<?php

$idCaja = Funciones::get('idCaja');

try {
	$caja = Factory::getInstance()->getCaja($idCaja);

	try {
		$usuarioPorCaja = Factory::getInstance()->getPermisoPorUsuarioPorCaja($caja->id, Usuario::logueado()->id, PermisosUsuarioPorCaja::verCaja);
		Html::jsonEncode('', array('importeEfectivo' => $caja->importeEfectivoFinal));
	} catch (FactoryExceptionRegistroNoExistente $ex) {
		Html::jsonEncode('', array('importeEfectivo' => 0));
	}

} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>