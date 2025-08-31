<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/finanzas/reportes/facturacion/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');
$docFAC = (Funciones::get('docFAC') == 'true') ? true : false;
$docNCR = (Funciones::get('docNCR') == 'true') ? true : false;
$docNDB = (Funciones::get('docNDB') == 'true') ? true : false;
$cliente = Funciones::get('cliente');
$orderBy = Funciones::get('orderBy');
$tipoReporte = Funciones::get('tipoReporte');
$esXls = Funciones::get('esXls', '1');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2xls->fileName = 'Reporte_Facturacion' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '') . (isset($cliente) ? '_' . $cliente : '');
	$html2xls->tituloReporte = 'Reporte Facturacion';
	$html2xls->datosCabecera = array('Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'),  'Cliente' => (isset($cliente) ? $cliente : '-'), 'Empresa' => (($empresa != 0) ? $empresa : 'Todas'), 'Facturas' => ($docFAC ? 'Si' : 'No'), 'Notas de debito' => ($docNDB ? 'Si' : 'No'), 'Notas de crédito' => ($docNCR ? 'Si' : 'No') );
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>