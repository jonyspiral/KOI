<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/cuenta_corriente_proveedor/buscar/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');
$nameProveedor = Funciones::get('nameProveedor');
$empresa = Funciones::get('empresa') ? Funciones::get('empresa') : '';

try {
	if (!isset($idProveedor)) {
		throw new Exception('Debe elegir un cliente');
	}
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Cuenta_corriente_' . $idProveedor . ($empresa ? '_' . $empresa : '');
	$html2xls->tituloReporte = 'Cuenta corriente';
	$html2xls->datosCabecera = array('Proveedor' => '[' . $idProveedor . '] ' . $nameProveedor, 'E' => ($empresa ? $empresa : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>