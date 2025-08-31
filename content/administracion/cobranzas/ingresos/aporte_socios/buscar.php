<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/aporte_socios/buscar/')) { ?>
<?php

$idAporte = Funciones::get('id');
$empresa = Funciones::session('empresa');

try {
	$aporteSocio = Factory::getInstance()->getAporteSocio($idAporte, $empresa);
	if ($aporteSocio->anulado()) {
		throw new FactoryExceptionCustomException('El aporte está anulado o fue modificado');
	}
	Html::jsonEncode('', $aporteSocio->expand());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>