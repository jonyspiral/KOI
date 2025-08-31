<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/remitos/reimpresion/buscar/')) { ?>
<?php

function snf($var) {
	if ($var == 'S' || $var == 'N')
		return $var;
	return false;
}

function strFechas($desde, $hasta){
	$strFechas = '';
	if (isset($desde) && isset($hasta)) {
		$strFechas = ' AND (fecha_remito >= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($desde)) . ')';
		$strFechas .= ' AND fecha_remito <= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . '))';
	} elseif (isset($desde))
		$strFechas = ' AND (fecha_remito >= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($desde)) . '))';
	elseif (isset($hasta))
		$strFechas = ' AND (fecha_remito <= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . ')) ';
	return $strFechas;
}

function jsonRemito(Remito $remito) {
	$json = array();
	$json['facturado'] = (isset($remito->facturaNumero) ? 'S' : 'N');
	$json['fecha'] = $remito->fecha;
	$json['idCliente'] = $remito->cliente->id;
	$json['razonSocialCliente'] = $remito->cliente->razonSocial;
	$json['importe'] = $remito->importe;
	$json['numero'] = $remito->numero;
	$json['anulado'] = $remito->anulado;
	$json['usuarioBaja'] = (empty($remito->idUsuarioBaja) ? '' : Factory::getInstance()->getUsuario($remito->idUsuarioBaja)->id . ' (' . $remito->fechaBaja . ')');
	return $json;
}

$empresa = Funciones::session('empresa');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$idCliente = Funciones::get('idCliente');
$numero = Funciones::get('numero');
$facturado = snf(Funciones::get('facturado'));

try {
	$where = Funciones::strFechas($desde, $hasta, 'fecha_remito') . ' AND (empresa = ' . Datos::objectToDB($empresa) . ')';
	if ($idCliente) $where .= ' AND (cod_cliente = ' . Datos::objectToDB($idCliente) . ')';
	if ($numero) $where .= ' AND (nro_remito = ' . Datos::objectToDB($numero) . ')';
	if ($facturado) $where .= ' AND (nro_factura IS ' . ($facturado == 'S' ? 'NOT ' : '') . 'NULL)';
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_remito DESC, cod_cliente ASC';

	$remitos = Factory::getInstance()->getListObject('Remito', $where . $order);
	if (count($remitos) == 0)
		throw new FactoryExceptionCustomException('No hay remitos con ese filtro');

	$arr = array();
	foreach ($remitos as $remito) {
		//Hago JSON el remito y lo meto en el array que voy a devolver
		$arr[] = jsonRemito($remito);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>