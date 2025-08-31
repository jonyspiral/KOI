<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/reimpresion/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');
!isset($empresa) && $empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$numero = Funciones::get('numero');
$letra = Funciones::get('letra');

try {
	$ncr = Factory::getInstance()->getNotaDeCredito($empresa, $puntoDeVenta, 'NCR', $numero, $letra);
	$ncr->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>