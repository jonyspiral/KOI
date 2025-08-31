<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/facturas/reimpresion/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');
!isset($empresa) && $empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$numero = Funciones::get('numero');
$letra = Funciones::get('letra');

try {
	$factura = Factory::getInstance()->getFactura($empresa, $puntoDeVenta, 'FAC', $numero, $letra);
	$factura->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>