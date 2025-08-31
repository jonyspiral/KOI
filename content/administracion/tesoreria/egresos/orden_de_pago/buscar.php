<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/egresos/orden_de_pago/buscar/')) { ?>
<?php

$id = Funciones::get('id');
$empresa = Funciones::session('empresa');

try {
	$ordenDePago = Factory::getInstance()->getOrdenDePago($id, $empresa);
	if ($ordenDePago->anulado()) {
		throw new FactoryExceptionCustomException('La orden de pago está anulada o fue modificada');
	}
	Html::jsonEncode('', $ordenDePago->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>