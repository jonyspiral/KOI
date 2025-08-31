<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/egresos/retiro_socios/buscar/')) { ?>
<?php

$id = Funciones::get('idRetiro');
$empresa = Funciones::session('empresa');

try {
	$retiroSocio = Factory::getInstance()->getRetiroSocio($id, $empresa);
	if ($retiroSocio->anulado()) {
		throw new FactoryExceptionCustomException('El retiro está anulado o fue modificado');
	}
	Html::jsonEncode('', $retiroSocio->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>