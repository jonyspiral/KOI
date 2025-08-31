<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_debito/reimpresion/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');
!isset($empresa) && $empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$numero = Funciones::get('numero');
$letra = Funciones::get('letra');

try {
	$ndb = Factory::getInstance()->getNotaDeDebito($empresa, $puntoDeVenta, 'NDB', $numero, $letra);
	$ndb->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>