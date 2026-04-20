<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/reimpresion/buscar/')) { ?>
<?php

function snf($var) {
	if ($var == 'S' || $var == 'N')
		return $var;
	return false;
}

function abef($var) {
	if ($var == 'A' || $var == 'B' || $var == 'E')
		return $var;
	return false;
}

function jsonNotaDeCredito($ncr) {
	/** @var $factura NotaDeCredito */
	$json = array();
	$json['empresa'] = $ncr->empresa;
	$json['caeObtenido'] = (isset($ncr->cae) ? 'S' : 'N');
	$json['mailEnviado'] = $ncr->mailEnviado;
	$json['fecha'] = $ncr->fecha;
	$json['idCliente'] = $ncr->cliente->id;
	$json['razonSocialCliente'] = $ncr->cliente->razonSocial;
	$json['importeTotal'] = $ncr->importeTotal;
	$json['puntoDeVenta'] = $ncr->puntoDeVenta;
	$json['letra'] = $ncr->letra;
	$json['numero'] = $ncr->numero;
	$json['numeroComprobante'] = $ncr->numeroComprobante;
	return $json;
}

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$caeObtenido = snf(Funciones::get('caeObtenido'));
$mailEnviado = snf(Funciones::get('mailEnviado'));
$idCliente = Funciones::get('idCliente');
$empresa = Funciones::session('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$letra = abef(Funciones::get('letra'));
$numero = Funciones::get('numero');

try {
	$where = Funciones::strFechas($desde, $hasta, 'fecha') . ' AND ';
	$where .= '(tipo_docum = ' . Datos::objectToDB('NCR') . ')';
	if ($idCliente) $where .= ' AND (cod_cliente = ' . Datos::objectToDB($idCliente) . ')';
	if ($empresa) $where .= ' AND (empresa = ' . Datos::objectToDB($empresa) . ')';
	if ($puntoDeVenta) $where .= ' AND (punto_venta = ' . Datos::objectToDB($puntoDeVenta) . ')';
	if ($caeObtenido) $where .= ' AND (cae IS ' . ($caeObtenido == 'S' ? 'NOT ' : '') . 'NULL)';
	if ($mailEnviado) $where .= ' AND (mail_enviado = ' . Datos::objectToDB($mailEnviado) . ')';
	if ($letra) $where .= ' AND (letra = ' . Datos::objectToDB($letra) . ')';
	if ($numero) $where .= ' AND (numero = ' . Datos::objectToDB($numero) . ')';
	if ($numeroComprobante) $where .= ' AND (nro_comprobante = ' . Datos::objectToDB($numeroComprobante) . ')';
	$where = trim($where, ' AND ') . ($where ? ' AND ' : '');
	$where .= ' (anulado = \'N\' OR anulado IS NULL) ';
	$order = ' ORDER BY fecha DESC, cae ASC, mail_enviado ASC, cod_cliente ASC, empresa ASC';

	$ncrs = Factory::getInstance()->getListObject('NotaDeCredito', $where . $order);
	if (count($ncrs) == 0)
		throw new FactoryExceptionCustomException('No hay notas de crédito con ese filtro');

	$arr = array();
	foreach ($ncrs as $ncr) {
		//Hago JSON la nota de crédito y la meto en el array que voy a devolver
		$arr[] = jsonNotaDeCredito($ncr);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>