<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/reimpresion_recibos/buscar/')) { ?>
<?php

$numero = Funciones::get('numero');
$empresa = Funciones::get('empresa');
!isset($empresa) && $empresa = Funciones::session('empresa');

try {
	$recibo = Factory::getInstance()->getRecibo($numero, $empresa);
	$recibo->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>