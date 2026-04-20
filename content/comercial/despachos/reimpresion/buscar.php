<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/despachos/reimpresion/buscar/')) { ?>
<?php

function snf($var) {
	if ($var == 'S' || $var == 'N')
		return $var;
	return false;
}

function strFechas($desde, $hasta){
	$strFechas = '';
	if (isset($desde) && isset($hasta)) {
		$strFechas = ' AND (fecha_alta >= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($desde)) . ')';
		$strFechas .= ' AND fecha_alta <= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . '))';
	} elseif (isset($desde))
		$strFechas = ' AND (fecha_alta >= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($desde)) . '))';
	elseif (isset($hasta))
		$strFechas = ' AND (fecha_alta <= dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . ')) ';
	return $strFechas;
}

function jsonDespachoItem(DespachoItem $despachoItem) {
	$json = array();
	$json['remitido'] = (isset($despachoItem->remitoNumero) ? 'S' : 'N');
	$json['fecha'] = $despachoItem->fechaAlta;
	$json['idCliente'] = $despachoItem->cliente->id;
	$json['razonSocialCliente'] = $despachoItem->cliente->razonSocial;
	$json['importe'] = $despachoItem->importeTotal;
	$json['numeroDespacho'] = $despachoItem->despacho->numero;
	$json['numeroItem'] = $despachoItem->numeroDeItem;
	$json['articulo'] = $despachoItem->idArticulo;
	$json['color'] = $despachoItem->idColorPorArticulo;
	$json['cantidad'] = $despachoItem->cantidadTotal;
	return $json;
}

$empresa = Funciones::session('empresa');
$idCliente = Funciones::get('idCliente');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$remitido = snf(Funciones::get('remitido'));
$numero = Funciones::get('numero');
$almacen = Funciones::get('almacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	$where = '(1 = 1)' . strFechas($desde, $hasta);
	if ($empresa) $where .= ' AND (empresa = ' . Datos::objectToDB($empresa) . ')';
	if ($idCliente) $where .= ' AND (cod_cliente = ' . Datos::objectToDB($idCliente) . ')';
	if ($remitido) $where .= ' AND (nro_remito IS ' . ($remitido == 'S' ? 'NOT ' : '') . 'NULL)';
	if ($numero) $where .= ' AND (nro_despacho = ' . Datos::objectToDB($numero) . ')';
	if ($almacen) $where .= ' AND (cod_almacen = ' . Datos::objectToDB($almacen) . ')';
	if ($idArticulo) $where .= ' AND (cod_articulo = ' . Datos::objectToDB($idArticulo) . ')';
	if ($idColor) $where .= ' AND (cod_color = ' . Datos::objectToDB($idColor) . ')';
	$where = trim($where, ' AND ') . ($where ? ' AND ' : '');
	$where .= ' (anulado = \'N\' OR anulado IS NULL) ';
	$order = ' ORDER BY fecha_alta DESC, cod_cliente ASC';

	$items = Factory::getInstance()->getListObject('DespachoItem', $where . $order);
	if (count($items) == 0)
		throw new FactoryExceptionCustomException('No hay despachos con ese filtro');

	$arr = array();
	foreach ($items as $item) {
		//Hago JSON el despachoItem y lo meto en el array que voy a devolver
		$arr[] = jsonDespachoItem($item);
	}
	Html::jsonEncode('', $arr);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>