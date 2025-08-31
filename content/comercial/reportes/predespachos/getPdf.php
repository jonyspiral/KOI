<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/reportes/predespachos/buscar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$tipo = Funciones::get('tipo');
$idCliente = Funciones::get('idCliente');
$idPedido = Funciones::get('idPedido');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$almacen = Funciones::get('almacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Predespachos_empresa_' . $empresa . ($tipo == 'C' ? '_por_cliente' : '_pedido_' . $idPedido);
	$html2pdf->tituloReporte = 'Predespachos';
	$html2pdf->datosCabecera = array('Empresa' => $empresa, 'Cliente' => (isset($idCliente) ? $idCliente : '-'), 'Pedido' => (isset($idPedido) ? $idPedido : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>