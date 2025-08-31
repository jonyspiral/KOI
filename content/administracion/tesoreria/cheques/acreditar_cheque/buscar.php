<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/acreditar_cheque/buscar/')) { ?>
<?php

function jsonCheque($cheque) {
	$json = array();
	$json['idCheque'] = $cheque->id;
	$json['nombreBanco'] = $cheque->banco->nombre;
	$json['numero'] = $cheque->numero;
	$json['cuitLibrador'] = $cheque->libradorCuit;
	$json['importe'] = $cheque->importe;
	$json['fechaVencimiento'] = $cheque->fechaVencimiento;
	return $json;
}

$idCuentaBancaria = Funciones::get('idCuentaBancaria');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$primeraVez = (Funciones::get('primeraVez') == 'S' ? true : false);
$arr = array();

try {
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_vencimiento') . ' AND ';
	$where .= ' AND anulado = ' . Datos::objectToDB('N');
	$where .= ' AND esperando_en_banco = ' . Datos::objectToDB('C');
	$where .= ' AND cod_rechazo_cheque IS NULL';
	$where .= ' AND fecha_credito_debito IS NULL';
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_vencimiento DESC';

	$cheques = Factory::getInstance()->getListObject('Cheque', $where . $order);
	if (!$primeraVez && count($cheques) == 0)
		throw new FactoryExceptionCustomException('No hay cheques para acreditar con ese filtro');

	try{
		$cuentaBancaria = Factory::getInstance()->getCuentaBancaria($idCuentaBancaria);
	}catch(FactoryExceptionRegistroNoExistente $ex){
		$cheques = array();
	}

	$arr = array();
	foreach ($cheques as $cheque) {
		//Hago JSON el cheque y lo meto en el array que voy a devolver
		(is_null($cuentaBancaria->id) || ($cheque->cajaActual == $cuentaBancaria->caja)) && $arr[] = jsonCheque($cheque);
	}

	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar buscar cheques');
}

?>
<?php } ?>