<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/prestamo/buscar/')) { ?>
<?php

$idPrestamo = Funciones::get('idPrestamo');
$empresa = Funciones::session('empresa');

try {
	$prestamo = Factory::getInstance()->getPrestamo($idPrestamo, $empresa);
	if ($prestamo->anulado()) {
		throw new FactoryExceptionCustomException('El prestamo está anulado o fue modificado');
	}
	Html::jsonEncode('', $prestamo->expand());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>