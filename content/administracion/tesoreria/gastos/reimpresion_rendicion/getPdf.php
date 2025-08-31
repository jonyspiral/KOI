<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/reimpresion_rendicion/buscar/')) { ?>
<?php

$numero = Funciones::get('numero');
$empresa = Funciones::session('empresa');

try {
	$rendicionDeGastos = Factory::getInstance()->getRendicionGastos($numero, $empresa);
	$rendicionDeGastos->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>