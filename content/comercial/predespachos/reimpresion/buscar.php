<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/predespachos/reimpresion/buscar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$idCliente = Funciones::get('idCliente');
$numero = Funciones::get('numero');
$almacen = Funciones::get('almacen');

try {
	$where = 'predespachados > 0  AND ';
	$where .= 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= (empty($idCliente) ? '' : 'cod_cliente = ' . Datos::objectToDB($idCliente) . ' AND ');
	$where .= (empty($almacen) ? '' : 'cod_almacen = ' . Datos::objectToDB($almacen) . ' AND ');
	$where = trim($where, ' AND ');
	$where .= ' GROUP BY cod_cliente, cod_sucursal';
	$orderBy = ' ORDER BY cod_cliente, cod_sucursal';;

	$items = Factory::getInstance()->getArrayFromView('predespachos_v', $where . $orderBy, 0, 'cod_cliente idCliente, cod_sucursal idSucursal, SUM(predespachados) pendientePredespacho');

	if (count($items) == 0) {
		throw new FactoryExceptionCustomException('No hay predespachos con ese filtro');
	}

	$arr = array();
	foreach ($items as $item) {
		$sucursal = Factory::getInstance()->getSucursal($item['idCliente'], $item['idSucursal']);
		$item['razonSocial'] = $sucursal->cliente->razonSocial;
		$item['esCasaCentral'] = $sucursal->esCasaCentral;
		$item['nombreSucursal'] = $sucursal->nombre;
		$arr[] = $item;
	}

	Html::jsonEncode('', $arr);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>