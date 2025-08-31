<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/calidad/devoluciones_a_clientes/buscar/')) { ?>
<?php

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$idCliente = Funciones::get('idCliente');

try {
	$where = Funciones::strFechas($desde, $hasta, 'fecha') . ' AND ';
	if ($idCliente) $where .= ' AND (cod_cliente = ' . Datos::objectToDB($idCliente) . ')';
	$where = trim($where, ' AND ');
	$where .=  ($where ? ' AND ' : '') . ' (anulado = ' . Datos::objectToDB('N') . ' OR anulado IS NULL) ';
	$order = ' ORDER BY fecha_alta DESC, cod_cliente ASC';

	$devoluciones = Factory::getInstance()->getListObject('DevolucionACliente', $where . $order);
	if (count($devoluciones) == 0) {
		throw new FactoryExceptionCustomException('No hay devoluciones con ese filtro');
	}

	$arr = array();
	foreach ($devoluciones as $dev) {
		$arr[] = $dev->expand();
	}
	Html::jsonEncode('', $arr);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>