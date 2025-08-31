<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/historico/buscar/')) { ?>
<?php

$cliente = Funciones::get('cliente');
$vendedor =Funciones::get('vendedor');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$clienteName = Funciones::get('clienteName');
$vendedorName = Funciones::get('vendedorName');
$razonSocial = Funciones::sacarTildes($clienteName);
$razonSocial = str_replace(' ', '_', $razonSocial);

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Pedidos_Historico' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '') . (isset($razonSocial) ? '_' . $razonSocial : ''). (isset($vendedor) ? '_' . $vendedor : '');
	$html2pdf->tituloReporte = 'Pedidos histórico';
	$html2pdf->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta' => (isset($hasta) ? $hasta : '-'),  'Cliente' => (isset($clienteName) ? $clienteName : '-'), 'Vendedor' => (isset($vendedorName) ? $vendedorName : '-') );
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>