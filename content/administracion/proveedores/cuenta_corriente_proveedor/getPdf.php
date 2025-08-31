<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/cuenta_corriente_proveedor/buscar/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');
$nameProveedor = Funciones::get('nameProveedor');
$empresa = Funciones::get('empresa') ? Funciones::get('empresa') : '';

try {
	if (!isset($idProveedor)) {
		throw new Exception('Debe elegir un cliente');
	}
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Cuenta_corriente_' . $idProveedor . ($empresa ? '_' . $empresa : '');
	$html2pdf->tituloReporte = 'Cuenta corriente';
	$html2pdf->datosCabecera = array('Proveedor' => '[' . $idProveedor . '] ' . $nameProveedor, 'E' => ($empresa ? $empresa : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>