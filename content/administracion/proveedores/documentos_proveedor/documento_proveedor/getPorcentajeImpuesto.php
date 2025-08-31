<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/documentos_proveedor/documento_proveedor/')) { ?>
<?php

$idImpuesto = Funciones::get('idImpuesto');

try {
	$impuesto = Factory::getInstance()->getImpuesto($idImpuesto);

	Html::jsonEncode('', array(
		'porcentaje' => $impuesto->porcentaje
	));
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>