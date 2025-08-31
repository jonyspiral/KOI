<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/ingreso_deposito_bancario/buscar/')) { ?>
<?php

function expand($cheque) {
	/** @var $cheque Cheque */
	$cheque->banco;
	return $cheque;
}

$empresa = Funciones::session('empresa');
$idDeposito = Funciones::get('idDeposito');
$filtros = json_decode(Funciones::get('filtros'), true);
$idCaja = $filtros['idCaja'];
$fechaDesde = $filtros['fechaDesde'];
$fechaHasta = $filtros['fechaHasta'];
$diasDesde = $filtros['diasDesde'];
$diasHasta = $filtros['diasHasta'];
$importeDesde = $filtros['importeDesde'];
$importeHasta = $filtros['importeHasta'];
$order = $filtros['order'];

try {
	if (is_null($idCaja)) {
		throw new FactoryExceptionCustomException('Debe seleccionar la caja del cheque');
	}

	if($idDeposito){
		$deposito = Factory::getInstance()->getDepositoBancarioTemporal($idDeposito);
		$chequesIn = '(';
		foreach($deposito->cheques as $cheque){
			$chequesIn .= $cheque->id . ', ';
		}
		$chequesIn = trim($chequesIn, ', ');
		$chequesIn .= ')';
	}

	$orders = array(
		1 => 'fecha_vencimiento ASC',
		2 => 'fecha_vencimiento DESC',
		3 => 'dias_vencimiento ASC',
		4 => 'dias_vencimiento DESC',
		5 => 'importe ASC',
		6 => 'importe DESC'
	);
	$order = ' ORDER BY ' . (!empty($orders[$order]) ? $orders[$order] : $orders[1]);
	$where = 'GETDATE() < dbo.relativeDate(fecha_vencimiento, ' . Datos::objectToDB('tomorrow') . ', 1) AND ';
	$where .= 'cod_caja_actual = ' . Datos::objectToDB($idCaja) . ' AND ';
	$where .= 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= '(dias_vencimiento BETWEEN -31 AND 0) AND ';
	$where .= 'esperando_en_banco IS NULL AND ';
	$where .= 'cod_cuenta_bancaria IS NULL AND ';
	$where .= 'cod_rechazo_cheque IS NULL AND ';
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= '(concluido = ' . Datos::objectToDB('N') . (empty($chequesIn) ? '' : ' OR cod_cheque IN ' . $chequesIn) . ' ) AND';
	$where .= !empty($fechaDesde) ? ('fecha_vencimiento >= dbo.toDate(' . Datos::objectToDB($fechaDesde) . ') AND ') : '';
	$where .= !empty($fechaHasta) ? ('fecha_vencimiento <= dbo.toDate(' . Datos::objectToDB($fechaHasta) . ') AND ') : '';
	$where .= !empty($diasDesde) ? ('dias_vencimiento >= ' . Datos::objectToDB($diasDesde) . ' AND ') : '';
	$where .= !empty($diasHasta) ? ('dias_vencimiento <= ' . Datos::objectToDB($diasHasta) . ' AND ') : '';
	$where .= !empty($importeDesde) ? ('importe >= ' . Datos::objectToDB($importeDesde) . ' AND ') : '';
	$where .= !empty($importeHasta) ? ('importe <= ' . Datos::objectToDB($importeHasta) . ' AND ') : '';
	$where = trim($where, ' AND ');
	$cheques = Factory::getInstance()->getListObject('Cheque', $where . $order);
	foreach ($cheques as &$cheque) {
		$cheque = expand($cheque);
	}

	Html::jsonEncode('', $cheques);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>