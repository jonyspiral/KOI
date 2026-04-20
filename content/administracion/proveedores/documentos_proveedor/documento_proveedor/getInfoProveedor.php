<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/documentos_proveedor/documento_proveedor/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');
$fechaDocumento = Funciones::get('fechaDocumento');
$fechaVencimiento = '';
try {
	$proveedor = Factory::getInstance()->getProveedor($idProveedor);
	if(!empty($fechaDocumento)){
		$fechaVencimiento = (is_null($proveedor->plazoPago) ? Funciones::hoy() : Funciones::sumarTiempo($fechaDocumento, $proveedor->plazoPago, 'days', 'd/m/Y'));
	}
	Html::jsonEncode('', array(
		'letra' => $proveedor->condicionIva->letraFacturaProveedor,
		'fechaVto' => $fechaVencimiento,
		'imputacionProveedor' => $proveedor->imputacionEspecifica
	));
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>