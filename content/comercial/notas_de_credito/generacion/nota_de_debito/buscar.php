<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/notas_de_credito/generacion/nota_de_debito/buscar/')) { ?>
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

function jsonNotaDeDebito($ndb) {
	$json = array();
	$json['caeObtenido'] = (isset($ndb->cae) ? 'S' : 'N');
	$json['mailEnviado'] = $ndb->mailEnviado;
	$json['fecha'] = $ndb->fecha;
	$json['idCliente'] = $ndb->cliente->id;
	$json['razonSocialCliente'] = $ndb->cliente->razonSocial;
	$json['importeTotal'] = $ndb->importeTotal;
	$json['puntoDeVenta'] = $ndb->puntoDeVenta;
	$json['letra'] = $ndb->letra;
	$json['numero'] = $ndb->numero;
	$json['numeroComprobante'] = $ndb->numeroComprobante;
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
	$where = '(tipo_docum = ' . Datos::objectToDB(TiposDocumento::notaDeDebito) . ')' . strFechas($desde, $hasta);
	if ($idCliente) $where .= ' AND (cod_cliente = ' . Datos::objectToDB($idCliente) . ')';
	if ($empresa) $where .= ' AND (empresa = ' . Datos::objectToDB($empresa) . ')';
	if ($puntoDeVenta) $where .= ' AND (punto_venta = ' . Datos::objectToDB($puntoDeVenta) . ')';
	if ($caeObtenido) $where .= ' AND (cae IS ' . ($caeObtenido == 'S' ? 'NOT ' : '') . 'NULL)';
	if ($mailEnviado) $where .= ' AND (mail_enviado = ' . Datos::objectToDB($mailEnviado) . ')';
	if ($letra) $where .= ' AND (letra = ' . Datos::objectToDB($letra) . ')';
	if ($numero) $where .= ' AND (numero = ' . Datos::objectToDB($numero) . ')';
	$where .= ' AND (anulado = ' . Datos::objectToDB('N') . ' OR anulado IS NULL) ';
	$where .= ' AND (cancel_nro_documento IS NULL) ';
	$order = ' ORDER BY fecha DESC, cae ASC, mail_enviado ASC, cod_cliente ASC, empresa ASC';

	$notasDeDebito = Factory::getInstance()->getListObject('NotaDeDebito', $where . $order);
	if (count($notasDeDebito) == 0)
		throw new FactoryExceptionCustomException('No hay notas de débito con ese filtro');

	$arr = array();
	foreach ($notasDeDebito as $ndb) {
		//Hago JSON la NDB y la meto en el array que voy a devolver
		$arr[] = jsonNotaDeDebito($ndb);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>