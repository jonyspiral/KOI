<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/ingreso_deposito_bancario/buscar/')) { ?>
<?php

$idCaja = Funciones::get('idCaja');

try {
	$caja = Factory::getInstance()->getCaja($idCaja);

	Html::jsonEncode('', array(
		'importeEfectivo' => $caja->importeEfectivoFinal
	));
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>