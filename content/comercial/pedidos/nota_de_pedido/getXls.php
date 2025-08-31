<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/nota_de_pedido/buscar/')) { ?>
<?php

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('getTablaArticulosXls.php');
	$html2xls->fileName = 'Nota_pedido_' . Funciones::formatearFecha(Funciones::hoy(), 'd-m-Y');
	$html2xls->tituloReporte = 'Nota de pedido';
	$html2xls->datosCabecera = array(
		'Razon social' => '',
		'Fecha impresion' => Funciones::hoy(),
		'Dom. entrega' => '',
		'Fecha pedido' => '',
		'Zona / Transp' => '',
		'Cond. pago' => '',
		'Vendedor' => '',
		'Cond. fact' => ''
	);
	
	$html2xls->excel->setOrientacionHorizontal();
	$html2xls->excel->columnWidth('A', 9);
	$html2xls->excel->columnWidth('B', 33);
	$html2xls->excel->columnWidth('C', 6);
	$html2xls->excel->columnWidth('D', 9);
	$html2xls->excel->columnWidth('E', 9);
	$html2xls->excel->columnWidth('F', 6);
	$html2xls->excel->columnWidth('G', 6);
	$html2xls->excel->columnWidth('H', 4);
	$html2xls->excel->columnWidth('I', 3);
	$html2xls->excel->columnWidth('J', 4);
	$html2xls->excel->columnWidth('K', 3);
	$html2xls->excel->columnWidth('L', 4);
	$html2xls->excel->columnWidth('M', 3);
	$html2xls->excel->columnWidth('N', 4);
	$html2xls->excel->columnWidth('O', 3);
	$html2xls->excel->columnWidth('P', 4);
	$html2xls->excel->columnWidth('Q', 3);
	$html2xls->excel->columnWidth('R', 4);
	$html2xls->excel->columnWidth('S', 3);
	$html2xls->excel->columnWidth('T', 4);
	$html2xls->excel->columnWidth('U', 3);
	$html2xls->excel->columnWidth('V', 4);
	$html2xls->excel->columnWidth('W', 3);
	$html2xls->excel->columnWidth('X', 7);
	$html2xls->excel->columnWidth('Y', 10);

	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>