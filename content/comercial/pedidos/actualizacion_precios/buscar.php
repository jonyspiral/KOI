<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/actualizacion_precios/buscar/')) { ?>
<?php

function jsonArrayDetalles($idPedido) {
	$arr = array();
	$pedido = Factory::getInstance()->getPedido($idPedido);

	$arr['idPedido'] = $pedido->numero;
	$arr['fecha'] = $pedido->fechaAlta;
	$arr['cliente'] = $pedido->cliente->getIdNombre();
	$arr['paresPendientes'] = $pedido->paresPendientes;

	return $arr;
}

$idCliente = Funciones::get('idCliente');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$arr = array();

try {
	$strFechas = Funciones::strFechas($desde, $hasta, 'fecha_alta');
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'pendiente > 0 AND ';
	$where .= 'cod_cliente != ' . Datos::objectToDB(663) . ' AND ';
	$where .= (empty($idCliente) ? '' : 'cod_cliente = ' . Datos::objectToDB($idCliente) . ' AND ');
	$where .= (empty($strFechas) ? '' : $strFechas . ' AND ');
	$where = trim($where, ' AND ');

	$pedidos = Factory::getInstance()->getArrayFromView('pedidos_d_v', $where . $orderBy, 0, 'DISTINCT nro_pedido');

	if (count($pedidos) == 0) {
		throw new FactoryExceptionCustomException('No hay pedidos pendientes con los filtros especificados');
	}

	foreach ($pedidos as $pedido) {
		$arr[] = jsonArrayDetalles($pedido['nro_pedido']);
	}

	Html::jsonEncode('', $arr);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>