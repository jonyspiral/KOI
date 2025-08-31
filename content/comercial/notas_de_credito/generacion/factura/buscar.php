<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/factura/buscar/')) { ?>
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

function strFechas($desde, $hasta){
	$strFechas = '';
	if (isset($desde) && isset($hasta)) {
		$strFechas = ' AND (fecha >= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($desde)) . ')';
		$strFechas .= ' AND fecha <= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . '))';
	} elseif (isset($desde))
		$strFechas = ' AND (fecha >= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($desde)) . '))';
	elseif (isset($hasta))
		$strFechas = ' AND (fecha <= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . ')) ';
	return $strFechas;
}

function jsonFactura($factura) {
	$json = array();
	$json['caeObtenido'] = (isset($factura->cae) ? 'S' : 'N');
	$json['mailEnviado'] = $factura->mailEnviado;
	$json['fecha'] = $factura->fecha;
	$json['idCliente'] = $factura->cliente->id;
	$json['razonSocialCliente'] = $factura->cliente->razonSocial;
	$json['importeTotal'] = $factura->importeTotal;
	$json['puntoDeVenta'] = $factura->puntoDeVenta;
	$json['letra'] = $factura->letra;
	$json['numero'] = $factura->numero;
	$json['numeroComprobante'] = $factura->numeroComprobante;
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
	$where = '(tipo_docum = ' . Datos::objectToDB('FAC') . ')' . strFechas($desde, $hasta);
	if ($idCliente) $where .= ' AND (cod_cliente = ' . Datos::objectToDB($idCliente) . ')';
	if ($empresa) $where .= ' AND (empresa = ' . Datos::objectToDB($empresa) . ')';
	if ($puntoDeVenta) $where .= ' AND (punto_venta = ' . Datos::objectToDB($puntoDeVenta) . ')';
	if ($caeObtenido) $where .= ' AND (cae IS ' . ($caeObtenido == 'S' ? 'NOT ' : '') . 'NULL)';
	if ($mailEnviado) $where .= ' AND (mail_enviado = ' . Datos::objectToDB($mailEnviado) . ')';
	if ($letra) $where .= ' AND (letra = ' . Datos::objectToDB($letra) . ')';
	if ($numero) $where .= ' AND (numero = ' . Datos::objectToDB($numero) . ')';
	$where .= ' AND (anulado = \'N\' OR anulado IS NULL) ';
	$where .= ' AND (cancel_nro_documento IS NULL) ';
	$order = ' ORDER BY fecha DESC, cae ASC, mail_enviado ASC, cod_cliente ASC, empresa ASC';

	$facturas = Factory::getInstance()->getListObject('Factura', $where . $order);
	if (count($facturas) == 0)
		throw new FactoryExceptionCustomException('No hay facturas con ese filtro');

	$arr = array();
	foreach ($facturas as $factura) {
		//Hago JSON la factura y la meto en el array que voy a devolver
		$arr[] = jsonFactura($factura);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>