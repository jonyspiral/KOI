<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/vendedores/reimpresion_documentos/buscar/')) { ?>
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

function getTiposDocumentos($tiposDocumentos) {
	//Necesito esta función para que sólo me pueda pedir estos 3 tipos de documentos (sin Recibos)
	$basicArray = array('FAC', 'NDB', 'NCR');
	if (!count($tiposDocumentos)) {
		return $basicArray;
	}
	for ($i = 0; $i < count($tiposDocumentos); $i++) {
		if (!in_array($tiposDocumentos[$i], $basicArray)) {
			unset($tiposDocumentos[$i]);
		}
	}
	return $tiposDocumentos;
}

function jsonDocumento($documento) {
	/** @var $documento Documento */
	$json = array();
	$json['empresa'] = $documento['empresa'];
	$json['caeObtenido'] = (isset($documento['cae']) ? 'S' : 'N');
	$json['mailEnviado'] = $documento['mail_enviado'];
	$json['fecha'] = $documento['fecha'];
	$json['idCliente'] = $documento['cod_cliente'];
	$json['razonSocialCliente'] = $documento['razon_social'];
	$json['importeTotal'] = $documento['importe_total'];
	$json['puntoDeVenta'] = $documento['punto_venta'];
	$json['tipoDocumento'] = $documento['tipo_docum'];
	$json['letra'] = $documento['letra'];
	$json['numero'] = $documento['numero'];
	$json['numeroComprobante'] = $documento['nro_comprobante'] ? $documento['nro_comprobante'] : $documento['numero'];
	return $json;
}

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$caeObtenido = snf(Funciones::get('caeObtenido'));
$mailEnviado = snf(Funciones::get('mailEnviado'));
$idCliente = Funciones::get('idCliente');
$empresa = Funciones::get('empresa');
$puntoDeVenta = Funciones::get('puntoDeVenta');
$letra = abef(Funciones::get('letra'));
$docFAC = (Funciones::get('docFAC') == 'true') ? true : false;
$docNCR = (Funciones::get('docNCR') == 'true') ? true : false;
$docNDB = (Funciones::get('docNDB') == 'true') ? true : false;
$numero = Funciones::get('numero');
$numeroComprobante = Funciones::get('numeroComprobante');
$idVendedor = (Usuario::logueado()->esPersonal() ? false : Usuario::logueado()->getCodigoPersonal());

try {
	if(!($docFAC || $docNDB || $docNCR)) {
		throw new FactoryExceptionCustomException('Debe seleccionar al menos un tipo de documento');
	}

	$where = Funciones::strFechas($desde, $hasta, 'fecha');
	if ($idCliente) $where .= ' AND (cod_cliente = ' . Datos::objectToDB($idCliente) . ')';
	if ($idVendedor) $where .= ' AND (cod_vendedor = ' . Datos::objectToDB($idVendedor) . ')';
	if ($empresa == '1' || $empresa == '2') $where .= ' AND (empresa = ' . Datos::objectToDB($empresa) . ')';
	if ($puntoDeVenta) $where .= ' AND (punto_venta = ' . Datos::objectToDB($puntoDeVenta) . ')';
	if ($caeObtenido) $where .= ' AND (cae IS ' . ($caeObtenido == 'S' ? 'NOT ' : '') . 'NULL)';
	if ($mailEnviado) $where .= ' AND (mail_enviado = ' . Datos::objectToDB($mailEnviado) . ')';
	if (count($tipoDocumento)) $where .= ' AND (tipo_docum IN ' . Datos::objectToDB($tipoDocumento) . ')';
	$where .= ' AND (';
	$where .=  ($docFAC ? 'tipo_docum = ' . Datos::objectToDB('FAC') . ' OR ' : '');
	$where .=  ($docNDB ? 'tipo_docum = ' . Datos::objectToDB('NDB') . ' OR ' : '');
	$where .=  ($docNCR ? 'tipo_docum = ' . Datos::objectToDB('NCR') . ' OR ' : '');
	$where = rtrim($where, ' OR ');
	$where .= ')';
	if ($letra) $where .= ' AND (letra = ' . Datos::objectToDB($letra) . ')';
	if ($numero) $where .= ' AND (numero = ' . Datos::objectToDB($numero) . ')';
	if ($numeroComprobante) $where .= ' AND (nro_comprobante = ' . Datos::objectToDB($numeroComprobante) . ')';
	$where = trim($where, ' AND ') . ($where ? ' AND ' : '');
	$where .= ' (anulado = ' . Datos::objectToDB('N') . ' OR anulado IS NULL) ';
	$order = ' ORDER BY fecha DESC, cae ASC, mail_enviado ASC, cod_cliente ASC, empresa ASC';

	$documentos = Factory::getInstance()->getArrayFromView('documentos_vendedor', $where . $order);
	if (count($documentos) == 0)
		throw new FactoryExceptionCustomException('No hay documentos con ese filtro');

	$arr = array();
	foreach ($documentos as $documento) {
		//Hago JSON la documento y la meto en el array que voy a devolver
		$arr[] = jsonDocumento($documento);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>