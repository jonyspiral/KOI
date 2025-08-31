<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/reportes/listado_clientes/buscar/')) { ?>
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
Funciones::get('esXls', true);


try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Listado_clientes' . (isset($idVendedor) ? '_vendedor_' . $idVendedor : '') . (isset($idPais) ? '_' . $idPais : '') . (isset($idProvincia) ? '_' . $idProvincia : '') . (isset($idLocalidad) ? '_' . $idLocalidad : '');
	$html2xls->tituloReporte = 'Listado clientes';
	$html2xls->datosCabecera = array('Vendedor' => (isset($idVendedor) ? $idVendedor : '-'), 'Pais' => (isset($idPais) ? $idPais : '-'), 'Provincia' => (isset($idProvincia) ? $idProvincia : '-'), 'Localidad' => (isset($idLocalidad) ? $localidad->nombre : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>