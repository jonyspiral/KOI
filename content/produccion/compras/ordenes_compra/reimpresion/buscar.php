<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/reimpresion/buscar/')) { ?>
<?php

function jsonOC(OrdenDeCompra $ordenDeCompra) {
	$json = array();
	$json['id'] = $ordenDeCompra->id;
	$json['proveedor'] = $ordenDeCompra->proveedor->getIdNombre();
	$json['fecha'] = $ordenDeCompra->fechaEmision;
	$json['importe'] = $ordenDeCompra->importeTotal;
	$json['cantItems'] = count($ordenDeCompra->detalle);

	return $json;
}

$idProveedor = Funciones::get('idProveedor');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$numero = Funciones::get('numero');

try {
	$strFechas = Funciones::strFechas($desde, $hasta, 'fecha_alta');

	$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'es_hexagono = ' . Datos::objectToDB('N') . ' AND ';
	$where .= (empty($strFechas) ? '' : $strFechas . ' AND ');
	$where .= (empty($idProveedor) ? '' : 'cod_proveedor = ' . $idProveedor . ' AND ');
	$where .= (empty($numero) ? '' : 'cod_orden_de_compra = ' . $numero . ' AND ');
	$where = trim($where, ' AND ');
	$where = (empty($where) ? '1 = 1' : $where);
	$order = ' ORDER BY fecha_alta DESC';

	$ordenesDecompra = Factory::getInstance()->getListObject('OrdenDeCompra', $where . $order);

	if (count($ordenesDecompra) == 0)
		throw new FactoryExceptionCustomException('No hay órdenes de compra con ese filtro');

	$arr = array();
	foreach ($ordenesDecompra as $ordenDeCompra) {
		$arr[] = jsonOC($ordenDeCompra);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>