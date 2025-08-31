<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/ingreso_deposito_bancario/buscar/')) { ?>
<?php

function expand($deposito) {
	/** @var $deposito DepositoBancarioTemporal */
	$arr = array();
	$arr['id'] = $deposito->id;
	$arr['nombreCaja'] = $deposito->caja->nombre;
	$arr['nombreCuenta'] = $deposito->cuentaBancaria->nombreCuenta;
	$arr['cantCheques'] = count($deposito->cheques);
	$arr['efectivo'] = $deposito->efectivo;
	$arr['esVentaCheques'] = $deposito->ventaCheque;

	return $arr;
}

try {
	$order = ' ORDER BY fecha_alta';
	$where = 'confirmado = ' . Datos::objectToDB('N');
	$where .= ' AND anulado = ' . Datos::objectToDB('N');

	$arr = array();
	$depositos = Factory::getInstance()->getListObject('DepositoBancarioTemporal', $where . $order);
	foreach ($depositos as $deposito) {
		$arr[] = expand($deposito);
	}

	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>