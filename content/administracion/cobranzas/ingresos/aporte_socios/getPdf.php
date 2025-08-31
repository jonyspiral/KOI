<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/aporte_socios/buscar/')) { ?>
<?php

$id = Funciones::get('idAporte');
$empresa = Funciones::session('empresa');

try {
	$retiroSocio = Factory::getInstance()->getAporteSocio($id, $empresa);
	if ($retiroSocio->anulado()) {
		throw new FactoryExceptionCustomException('El retiro está anulado o fue modificado');
	}

	$retiroSocio->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>