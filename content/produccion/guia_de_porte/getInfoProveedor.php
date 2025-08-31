<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('produccion/guia_de_porte/buscar/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');

try {
	$proveedor = Factory::getInstance()->getProveedor($idProveedor);
	Html::jsonEncode('', array(
		'idProveedor' => $proveedor->id,
		'direccionCalle' => $proveedor->direccionCalle,
		'direccionNumero' => $proveedor->direccionNumero,
		'direccionPiso' => $proveedor->direccionPiso,
		'direccionDepartamento' => $proveedor->direccionDepartamento,
		'direccionLocalidad' => $proveedor->direccionLocalidad->nombre,
		'direccionCodigoPostal' => $proveedor->direccionCodigoPostal,
		'cuit' => $proveedor->cuit,
		'condicionIva' => $proveedor->condicionIva
	));
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>