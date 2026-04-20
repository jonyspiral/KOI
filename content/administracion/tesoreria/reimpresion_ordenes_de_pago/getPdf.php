<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/reimpresion_ordenes_de_pago/buscar/')) { ?>
<?php

$numero = Funciones::get('numero');
$empresa = Funciones::session('empresa');

try {
	$ordenDePago = Factory::getInstance()->getOrdenDePago($numero, $empresa);
	$ordenDePago->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>