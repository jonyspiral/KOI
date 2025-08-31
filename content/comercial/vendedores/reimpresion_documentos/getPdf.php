<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/vendedores/reimpresion_documentos/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');
!isset($empresa) && $empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$numero = Funciones::get('numero');
$tipoDocumento = Funciones::get('tipoDocumento');
$letra = Funciones::get('letra');

try {
	$documento = Factory::getInstance()->getDocumento($empresa, $puntoDeVenta, $tipoDocumento, $numero, $letra);
	$documento->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>