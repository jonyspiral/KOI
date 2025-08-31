<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/reimpresion_ordenes_de_pago/buscar/')) { ?>
<?php

function snf($var) {
	if ($var == 'S' || $var == 'N')
		return $var;
	return false;
}

function jsonOrdenDePago(OrdenDePago $ordenDePago) {
	$json = array();
	$json['empresa'] = $ordenDePago->empresa;
	$json['mailEnviado'] = $ordenDePago->mailEnviado;
	$json['fecha'] = $ordenDePago->fecha;
	$json['idProveedor'] = $ordenDePago->proveedor->id;
	$json['razonSocialProveedor'] = $ordenDePago->proveedor->razonSocial;
	$json['importeTotal'] = $ordenDePago->importeTotal;
	$json['importePendiente'] = $ordenDePago->importePendiente;
	$json['numero'] = $ordenDePago->numero;
	$json['idImputacion'] = $ordenDePago->imputacion->id;
	$json['beneficiario'] = $ordenDePago->beneficiario;
	$json['observaciones'] = $ordenDePago->observaciones;
	$json['anulado'] = $ordenDePago->anulado;
	$json['usuarioBaja'] = (empty($ordenDePago->idUsuarioBaja) ? '' : Factory::getInstance()->getUsuario($ordenDePago->idUsuarioBaja)->id . ' (' . $ordenDePago->fechaBaja . ')');
	return $json;
}

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$tipo = snf(Funciones::get('tipo'));
$mailEnviado = snf(Funciones::get('mailEnviado'));
$idProveedor = Funciones::get('idProveedor');
$empresa = Funciones::session('empresa');
$numero = Funciones::get('numero');
$orderBy = Funciones::get('orderBy');

try {
	$arrayOrderBy = array(
		'0' => 'nro_orden_de_pago',
		'1' => 'fecha_documento'
	);

	$where = 'empresa = ' . Datos::objectToDB($empresa);
	if ($idProveedor) $where .= ' AND cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' ';
	if ($tipo) $where .= ' AND cod_proveedor ' . ($tipo == 'S' ? 'IS NULL' : 'IS NOT NULL');
	if ($mailEnviado) $where .= ' AND mail_enviado = ' . Datos::objectToDB($mailEnviado) . ' ';
	if ($numero) $where .= ' AND nro_orden_de_pago = ' . Datos::objectToDB($numero) . ' ';
	//$where .= ' AND anulado = ' . Datos::objectToDB('N');
	$strFechas = Funciones::strFechas($desde, $hasta, 'fecha_documento');
	$where .= (empty($strFechas) ? '' : ' AND ') . $strFechas;
	$order = ' ORDER BY ' . (empty($arrayOrderBy[$orderBy]) ? '' : $arrayOrderBy[$orderBy] . ' DESC, ') . 'mail_enviado ASC, cod_proveedor ASC, empresa ASC';

	$ordenesDePago = Factory::getInstance()->getListObject('OrdenDePago', $where . $order);
	if (count($ordenesDePago) == 0)
		throw new FactoryExceptionCustomException('No hay ordenes de pago con ese filtro');

	$arr = array();
	foreach ($ordenesDePago as $ordenDePago) {
		$arr[] = jsonOrdenDePago($ordenDePago);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>