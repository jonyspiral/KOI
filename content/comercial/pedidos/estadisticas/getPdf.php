<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/estadisticas/buscar/')) { ?>
<?php

$modo = Funciones::get('modo');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$idVendedor = Funciones::get('idVendedor');
$idCliente = Funciones::get('idCliente');
//$tipoProducto = Funciones::get('tipoProducto');
$tipoProducto = (Funciones::get('tipoProducto') ? explode(',', Funciones::get('tipoProducto')) : array());
$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Pedidos_Estadisticas' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '') . (isset($idCliente) ? '_' . $idCliente : '') . (isset($idVendedor) ? '_' . $idVendedor : '') . (isset($idAlmacen) ? '_' . $idAlmacen : '') . (isset($idArticulo) ? '_' . $idArticulo : '') . (isset($idColor) ? '_' . $idColor : '');
	$html2pdf->tituloReporte = 'Pedidos Estadisticas';
	$html2pdf->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta' => (isset($hasta) ? $hasta : '-'),  'Cliente' => (isset($idCliente) ? $idCliente : '-'), 'Vendedor' => (isset($idVendedor) ? $idVendedor : '-'), 'idAlmacen' => (isset($idAlmacen) ? $idAlmacen : '-'), 'idArticulo' => (isset($idArticulo) ? $idArticulo : '-'), 'idColor' => (isset($idColor) ? $idColor : '-') );
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>