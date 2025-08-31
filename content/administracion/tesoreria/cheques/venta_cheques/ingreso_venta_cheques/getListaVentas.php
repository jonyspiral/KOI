<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/venta_cheques/ingreso_venta_cheques/buscar/')) { ?>
<?php

function expand(VentaChequesTemporal $venta) {
	$arr = array();
	$arr['id'] = $venta->id;
	$arr['nombreCaja'] = $venta->caja->nombre;
	$arr['nombreCuenta'] = $venta->cuentaBancaria->nombreCuenta;
	$arr['cantCheques'] = count($venta->cheques);
	$arr['importeTotal'] = $venta->importeTotal;

	return $arr;
}

try {
	$order = ' ORDER BY fecha_alta';
	$where = 'confirmado = ' . Datos::objectToDB('N');
	$where .= ' AND anulado = ' . Datos::objectToDB('N');

	$arr = array();
	$ventas = Factory::getInstance()->getListObject('VentaChequesTemporal', $where . $order);
	foreach ($ventas as $venta) {
		$arr[] = expand($venta);
	}

	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>