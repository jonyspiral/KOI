<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/recibos/buscar/')) { ?>
<?php

$id = Funciones::get('id');
$empresa = Funciones::session('empresa');

try {
	$recibo = Factory::getInstance()->getRecibo($id, $empresa);
	if ($recibo->anulado()) {
		throw new FactoryExceptionCustomException('El recibo está anulado o fue modificado');
	}
	if ($recibo->esEcommerce()) {
		throw new FactoryExceptionCustomException('No se pueden editar recibos de ecommerce');
	}
	Html::jsonEncode('', $recibo->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>