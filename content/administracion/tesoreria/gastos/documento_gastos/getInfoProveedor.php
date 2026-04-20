<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/documento_gastos/')) { ?>
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
		'letra' => $proveedor->condicionIva->letraFactura,
		'fechaVto' => $fechaVencimiento,
		'imputacionProveedor' => $proveedor->imputacionEspecifica
	));
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>