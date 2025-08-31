<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/recibos/buscar/')) { ?>
<?php

$id = Funciones::get('id');
$empresa = Funciones::session('empresa');

try {
	$recibo = Factory::getInstance()->getRecibo($id, $empresa);
	if ($recibo->anulado()) {
		throw new FactoryExceptionCustomException('El recibo está anulado o fue modificado');
	}

	$recibo->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>