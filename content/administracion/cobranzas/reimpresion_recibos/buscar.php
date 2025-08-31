<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/reimpresion_recibos/buscar/')) { ?>
<?php

function snf($var) {
	if ($var == 'S' || $var == 'N')
		return $var;
	return false;
}

function jsonRecibo(Recibo $recibo) {
	$json = array();
	$json['empresa'] = $recibo->empresa;
	$json['mailEnviado'] = $recibo->mailEnviado;
	$json['fecha'] = $recibo->fecha;
	$json['idCliente'] = $recibo->cliente->id;
	$json['razonSocialCliente'] = $recibo->cliente->razonSocial;
	$json['importeTotal'] = $recibo->importeTotal;
	$json['importePendiente'] = $recibo->importePendiente;
	$json['numero'] = $recibo->numero;
	$json['idImputacion'] = $recibo->imputacion->id;
	$json['recibidoDe'] = $recibo->recibidoDe;
	$json['observaciones'] = $recibo->observaciones;
	return $json;
}

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$mailEnviado = snf(Funciones::get('mailEnviado'));
$idCliente = Funciones::get('idCliente');
$empresa = Funciones::session('empresa');
$numero = Funciones::get('numero');

try {
	$where = 'empresa = ' . Datos::objectToDB($empresa);
	if ($idCliente) $where .= ' AND cod_cliente = ' . Datos::objectToDB($idCliente) . ' ';
	if ($mailEnviado) $where .= ' AND mail_enviado = ' . Datos::objectToDB($mailEnviado) . ' ';
	if ($numero) $where .= ' AND nro_recibo = ' . Datos::objectToDB($numero) . ' ';
	$where .= ' AND anulado = ' . Datos::objectToDB('N');
	$strFechas = Funciones::strFechas($desde, $hasta, 'fecha_documento');
	$where .= (empty($strFechas) ? '' : ' AND ') . $strFechas;
	$order = ' ORDER BY fecha_documento DESC, mail_enviado ASC, cod_cliente ASC, empresa ASC';

	$recibos = Factory::getInstance()->getListObject('Recibo', $where . $order);
	if (count($recibos) == 0)
		throw new FactoryExceptionCustomException('No hay recibos con ese filtro');

	$arr = array();
	foreach ($recibos as $recibo) {
		$arr[] = jsonRecibo($recibo);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>