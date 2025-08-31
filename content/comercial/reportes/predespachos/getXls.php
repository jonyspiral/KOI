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
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Predespachos_empresa_' . $empresa . ($tipo == 'C' ? '_por_cliente' : '_pedido_' . $idPedido);
	$html2xls->tituloReporte = 'Predespachos';
	$html2xls->datosCabecera = array('Empresa' => $empresa, 'Cliente' => (isset($idCliente) ? $idCliente : '-'), 'Pedido' => (isset($idPedido) ? $idPedido : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>