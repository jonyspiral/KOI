<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/remitos/reimpresion/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');
!isset($empresa) && $empresa = Funciones::session('empresa');
$numero = Funciones::get('numero');
$letra = ($empresa == 1 ? 'R' : 'X');

try {
	$remito = Factory::getInstance()->getRemito($empresa, $numero, $letra);
	$remito->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>