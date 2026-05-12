<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/debitar_cheque/buscar/')) { ?>
<?php

function jsonCheque(Cheque $cheque) {
	$json = array();
	$json['idCheque'] = $cheque->id;
	$json['nombreBanco'] = $cheque->banco->nombre;
	$json['nombreCuenta'] = $cheque->cuentaBancaria->nombreCuenta;
	$json['numero'] = $cheque->numero;
	$json['cuitLibrador'] = $cheque->libradorCuit;
	$json['importe'] = $cheque->importe;
	$json['fechaVencimiento'] = $cheque->fechaVencimiento;
	$json['entregadoA'] = ($cheque->proveedor->razonSocial ? '[' . $cheque->proveedor->id . '] ' . $cheque->proveedor->razonSocial : '-');
	return $json;
}

$idCuentaBancaria = Funciones::get('idCuentaBancaria');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$primeraVez = (Funciones::get('primeraVez') == 'S' ? true : false);
$arr = array();

try {
	$where = ($primeraVez ? 'fecha_vencimiento < dbo.relativeDate(GETDATE(), ' . Datos::objectToDB('tomorrow') . ', 0) AND ' : Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_vencimiento') . 'AND ');
	$where .= 'anulado = ' . Datos::objectToDB('N');
	$where .= ' AND esperando_en_banco = ' . Datos::objectToDB('D');
	$where .= ' AND cod_rechazo_cheque IS NULL';
	$where .= ' AND fecha_credito_debito IS NULL';
	$where .= ' AND cod_cuenta_bancaria IS NOT NULL';
	$where .= (is_null($idCuentaBancaria) ? '' : ' AND cod_cuenta_bancaria = ' . Datos::objectToDB($idCuentaBancaria));
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_vencimiento DESC';

	$cheques = Factory::getInstance()->getListObject('Cheque', $where . $order);
	if (!$primeraVez && count($cheques) == 0)
		throw new FactoryExceptionCustomException('No hay cheques para debitar con ese filtro');

	$arr = array();
	$total = 0;
	foreach ($cheques as $cheque) {
		//Hago JSON el cheque y lo meto en el array que voy a devolver
		$arr[] = jsonCheque($cheque);
		$total += $cheque->importe;
	}
	Html::jsonEncode('', array('total' => $total, 'cheques' => $arr));

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar buscar cheques');
}

?>
<?php } ?>