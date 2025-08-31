<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/pendientes/buscar/')) { ?>
<?php

$idCliente= Funciones::get('cliente');
$vendedor=Funciones::get('vendedor');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$clienteName = Funciones::get('clienteName');
$vendedorName = Funciones::get('vendedorName');
$cliente = Factory::getInstance()->getCliente($idCliente);
$razonSocial = Funciones::sacarTildes($cliente->razonSocial);
$razonSocial = str_replace(' ', '_', $razonSocial);

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Pedidos_Pendientes' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '') . (isset($razonSocial) ? '_' . $razonSocial : ''). (isset($vendedor) ? '_' . $vendedor : '');
	$html2xls->tituloReporte = 'Pedidos Pendientes';
	$html2xls->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta' => (isset($hasta) ? $hasta : '-'),  'Cliente' => (isset($clienteName) ? $clienteName : '-'), 'Vendedor' => (isset($vendedorName) ? $vendedorName : '-') );
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>