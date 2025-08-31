<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/depositos_pendientes/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$nroRecibo = Funciones::get('nroRecibo');
$empresa = Funciones::get('empresa');

try {
	$strFechas = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_documento');

	$where = 'cod_cliente = ' . Datos::objectToDB(ParametrosGenerales::clienteDepositosPendientes) . ' AND ';
	$where .= (empty($strFechas) ? '' : $strFechas . ' AND ');
	$where .= (empty($nroRecibo) ? '' : 'nro_recibo = ' . Datos::objectToDB($nroRecibo) . ' AND ');
	$where .= (empty($empresa) ? '' : 'empresa = ' . Datos::objectToDB($empresa) . ' AND ');
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_documento';

	$items = Factory::getInstance()->getListObject('Recibo', $where . $order);

	if (!count($items)) {
		throw new FactoryExceptionCustomException('No hay depósitos pendientes de ser identificados');
	}

	$arrayHeader = array(
		array('content' => 'Fecha', 'dataType' => 'Center', 'width' => 10, 'title' => 'Fecha de cumplido'),
		array('content' => 'Nro. Recibo', 'dataType' => 'Center', 'width' => 10),
		array('content' => 'Observaciones', 'width' => 40),
		array('content' => 'Importe', 'dataType' => 'Moneda', 'width' => 10),
		array('content' => 'Cliente', 'dataType' => 'Center', 'width' => 20),
		array('content' => 'Acción', 'dataType' => 'Center', 'width' => 5),
	);

	$tabla = new HtmlTable(array('cantRows' => count($items), 'cantCols' => 6, 'class' => 'registrosAlternados', 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->createHeaderFromArray($arrayHeader);
	$i = 0;
	foreach ($items as $item) {
		/** @var Recibo $item */
		$cells[$i][0]->content = $item->fecha;
		$cells[$i][1]->content = $item->numero;
		$cells[$i][2]->content = $item->observaciones;
		$cells[$i][3]->content = $item->importeTotal;
		$cells[$i][4]->content = '<input id="inputCliente_' . $item->numero . '_' . $item->empresa . '" class="textbox obligatorio autoSuggestBox inputForm w230" name="ClienteTodos" />';
		$cells[$i][5]->content = '<a href="#" class="boton btnConfirmar" title="Confirmar" style="display: inline;" ';
		$cells[$i][5]->content .= 'data-numerorecibo="' . $item->numero . '" data-empresa="' . $item->empresa . '"><img src="/img/botones/25/aceptar.gif"></a>';

		$rows[$i]->id = 'row_' . $item->numero . '_' . $item->empresa;
		$i++;
	}

	$html = $tabla->create(true);
	echo $html;
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>