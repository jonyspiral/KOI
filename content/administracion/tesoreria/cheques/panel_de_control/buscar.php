<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/panel_de_control/buscar/')) { ?>
<?php

function jsonCheque(Cheque $cheque) {
	$json = array();
	$json['idCheque'] = $cheque->id;
	$json['nombreBanco'] = $cheque->banco->nombre;
	$json['cliente'] = $cheque->esDeCliente() ? $cheque->cliente->razonSocial : '';
	$json['nombreCuenta'] = $cheque->cuentaBancaria->nombre;
	$json['numero'] = $cheque->numero;
	$json['nombreLibrador'] = $cheque->libradorNombre;
	$json['cuitLibrador'] = $cheque->libradorCuit;
	$json['importe'] = $cheque->importe;
	$json['noALaOrden'] = $cheque->noALaOrden;
	$json['cruzado'] = $cheque->cruzado;
	$json['fechaEmision'] = $cheque->fechaEmision;
	$json['fechaVencimiento'] = $cheque->fechaVencimiento;
	$json['propio'] = ($cheque->esPropio() ? '1' : '0');
	$json['entregado'] = ($cheque->concluido() && ($cheque->entregadoProveedor()) ? '1' : '0');
	$json['reingresable'] = (!($cheque->rechazado() || $cheque->anulado() || !$cheque->concluido() || ($cheque->esperandoEnBanco && $cheque->concluido())) ? '1' : '0');
	return $json;
}

$idCliente = Funciones::get('idCliente');
$idCaja = Funciones::get('idCaja');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$importeDesde = Funciones::get('importeDesde');
$importeHasta = Funciones::get('importeHasta');
$idCuentaBancaria = Funciones::get('idCuentaBancaria');
$numeroCheque = Funciones::get('numeroCheque');
$tipoCheque = Funciones::get('tipoCheque');
$empresa = Funciones::session('empresa');
$arr = array();

try {
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_vencimiento');
	$uxcs = Factory::getInstance()->getListObject('PermisoPorUsuarioPorCaja', '(cod_usuario = ' . Datos::objectToDB(Usuario::logueado()->id) . ') AND (cod_permiso = ' . Datos::objectToDB(PermisosUsuarioPorCaja::verCaja) . ')');
	(count($uxcs)) && $where .= ' AND (';
	foreach ($uxcs as $uxc){
		/** @var PermisoPorUsuarioPorCaja $uxc */
		$where .= 'cod_caja_actual = ' . Datos::objectToDB($uxc->idCaja) . ' OR ';
	}
	(count($uxcs)) && $where = trim(trim($where, ' AND '), ' OR ') . ')';
	$where .= ' AND cod_caja_actual >= 100';
	$where .= ' AND empresa = ' . Datos::objectToDB($empresa);
	$where .= ' AND anulado = ' . Datos::objectToDB('N');
	$where .= (empty($idCliente) ? '' : ' AND cod_cliente = ' . Datos::objectToDB($idCliente));
	$where .= (empty($idCaja) ? '' : ' AND cod_caja_actual = ' . Datos::objectToDB($idCaja));
	$where .= (empty($importeDesde) ? '' : ' AND importe >= ' . Datos::objectToDB($importeDesde));
	$where .= (empty($importeHasta) ? '' : ' AND importe <= ' . Datos::objectToDB($importeHasta));
	$where .= (empty($idCuentaBancaria) ? '' : ' AND cod_cuenta_bancaria = ' . Datos::objectToDB($idCuentaBancaria));
	$where .= (empty($numeroCheque) ? '' : ' AND numero LIKE ' . Datos::objectToDB('%' . $numeroCheque . '%'));
	($tipoCheque != 'TOD') && $where .= ' AND cod_cuenta_bancaria IS ' . ($tipoCheque == 'PRO' ? 'NOT' : '') . ' NULL ';
	//$where .= ' AND ((concluido = ' . Datos::objectToDB('S') . ' AND esperando_en_banco IS NULL)';
	//$where .= ' OR (concluido = ' . Datos::objectToDB('N') . ' AND esperando_en_banco ' . ($tipoCheque == 'TOD' ? 'IS NOT NULL' : '= ' . ($tipoCheque == 'PRO' ? Datos::objectToDB('D') : Datos::objectToDB('C'))) . '))';
	$where .= ' AND anulado = ' . Datos::objectToDB('N');
	$where .= ' AND cod_rechazo_cheque IS NULL';
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_vencimiento DESC';

	$cheques = Factory::getInstance()->getListObject('Cheque', $where . $order);
	if (count($cheques) == 0) {
		throw new FactoryExceptionCustomException('No hay cheques con ese filtro');
	}

	$arr = array();
	foreach ($cheques as $cheque) {
		//Hago JSON el cheque y lo meto en el array que voy a devolver
		$arr[] = jsonCheque($cheque);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar buscar cheques');
}

?>
<?php } ?>