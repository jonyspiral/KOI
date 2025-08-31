<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/venta_cheques/reimpresion_venta_cheques/buscar/')) { ?>
<?php

function snf($var) {
	if ($var == 'S' || $var == 'N')
		return $var;
	return false;
}

function jsonVentaCheques(VentaChequesCabecera $ventaChequesCabecera) {
	$json = array();
	$json['empresa'] = $ventaChequesCabecera->empresa;
	$json['fecha'] = $ventaChequesCabecera->fecha;
	$json['importeTotal'] = $ventaChequesCabecera->detalle[0]->importeTotal;
	$json['numero'] = $ventaChequesCabecera->numero;
	$json['caja'] = '[' . $ventaChequesCabecera->detalle[0]->importePorOperacion->caja->id . '] ' . $ventaChequesCabecera->detalle[0]->importePorOperacion->caja->nombre;
	$json['observaciones'] = $ventaChequesCabecera->observaciones;
	return $json;
}

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$empresa = Funciones::session('empresa');
$numero = Funciones::get('numero');

try {
	$strFechas .= Funciones::strFechas($desde, $hasta, 'fecha_documento');
	$where = 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= (is_null($numero) ? '' : 'cod_venta_cheques = ' . Datos::objectToDB($numero) . ' AND ');
	$where .= (empty($strFechas) ? '' : $strFechas . ' AND ');
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_documento DESC';

	$ventaCheques = Factory::getInstance()->getListObject('VentaChequesCabecera', $where . $order);
	if (count($ventaCheques) == 0)
		throw new FactoryExceptionCustomException('No hay ventas de cheque con ese filtro');

	$arr = array();
	foreach ($ventaCheques as $ventaCheque) {
		$arr[] = jsonVentaCheques($ventaCheque);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>