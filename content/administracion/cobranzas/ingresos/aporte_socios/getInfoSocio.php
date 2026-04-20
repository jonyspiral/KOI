<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/aporte_socios/buscar/')) { ?>
<?php

$idSocio = Funciones::get('idSocio');

try {
	$socio = Factory::getInstance()->getSocio($idSocio);
	Html::jsonEncode('', array('cuit' => $socio->cuil, 'nombre' => $socio->nombre));
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>