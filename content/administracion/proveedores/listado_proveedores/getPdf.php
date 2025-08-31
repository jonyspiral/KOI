<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/listado_proveedores/buscar/')) { ?>
<?php

$cuit = Funciones::get('cuit');
$idVendedor = Funciones::get('idVendedor');
$idPais = Funciones::get('idPais');
$idProvincia = Funciones::get('idProvincia');
$idLocalidad = Funciones::get('idLocalidad');
$calle = Funciones::get('calle');
$numero = Funciones::get('numero');
$orderBy = Funciones::get('orderBy');
$localidad = Factory::getInstance()->getLocalidad($idPais, $idProvincia, $idLocalidad);

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Listado_clientes' . (isset($idVendedor) ? '_vendedor_' . $idVendedor : '') . (isset($idPais) ? '_' . $idPais : '') . (isset($idProvincia) ? '_' . $idProvincia : '') . (isset($idLocalidad) ? '_' . $idLocalidad : '');
	$html2pdf->tituloReporte = 'Listado clientes';
	$html2pdf->datosCabecera = array('Vendedor' => (isset($idVendedor) ? $idVendedor : '-'), 'Pais' => (isset($idPais) ? $idPais : '-'), 'Provincia' => (isset($idProvincia) ? $idProvincia : '-'), 'Localidad' => (isset($idLocalidad) ? $localidad->nombre : '-'));
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>