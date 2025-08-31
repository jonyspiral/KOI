<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/garantia/normal/buscar/')) { ?>
<?php

$idGarantia = Funciones::get('idGarantia');

try {
	$garantia = Factory::getInstance()->getGarantia($idGarantia);
	$garantia->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>