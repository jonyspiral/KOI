<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/cobro_cheques_ventanilla/reimpresion_cobro_cheques_ventanilla/buscar/')) { ?>
<?php

function snf($var) {
	if ($var == 'S' || $var == 'N')
		return $var;
	return false;
}

function jsonCobroCheques(CobroChequeVentanillaCabecera $cobroChequesCabecera) {
	$json = array();
	$json['empresa'] = $cobroChequesCabecera->empresa;
	$json['fecha'] = $cobroChequesCabecera->fecha;
	$json['responsable'] = $cobroChequesCabecera->responsable->nombreApellido;
	$json['importeTotal'] = $cobroChequesCabecera->detalle[0]->importeTotal;
	$json['numero'] = $cobroChequesCabecera->numero;
	$json['observaciones'] = $cobroChequesCabecera->observaciones;
	return $json;
}

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$empresa = Funciones::session('empresa');
$numero = Funciones::get('numero');

try {
	$where = 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= (is_null($numero) ? '' : 'cod_cobro_cheque_ventanilla = ' . Datos::objectToDB($numero) . ' AND ');
	$where .= Funciones::strFechas($desde, $hasta, 'fecha_documento') . ' AND ';
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_documento DESC';

	$cobroCheques = Factory::getInstance()->getListObject('CobroChequeVentanillaCabecera', $where . $order);
	if (count($cobroCheques) == 0)
		throw new FactoryExceptionCustomException('No hay cobros de cheques por ventanilla con ese filtro');

	$arr = array();
	foreach ($cobroCheques as $cobroCheque) {
		$arr[] = jsonCobroCheques($cobroCheque);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>