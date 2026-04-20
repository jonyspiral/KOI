<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/deposito_bancario/reimpresion_deposito_bancario/buscar/')) { ?>
<?php

function snf($var) {
	if ($var == 'S' || $var == 'N')
		return $var;
	return false;
}

function jsonOrdenDePago(DepositoBancarioCabecera $depositoBancarioCabecera) {
	$json = array();
	$json['empresa'] = $depositoBancarioCabecera->empresa;
	$json['fecha'] = $depositoBancarioCabecera->fecha;
	$json['numeroTransaccion'] = $depositoBancarioCabecera->numeroTransaccion;
	$json['ventaCheque'] = $depositoBancarioCabecera->ventaCheque;
	$json['importeTotal'] = $depositoBancarioCabecera->detalle[0]->importeTotal;
	$json['numero'] = $depositoBancarioCabecera->numero;
	$json['observaciones'] = $depositoBancarioCabecera->observaciones;
	return $json;
}

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$empresa = Funciones::session('empresa');
$numero = Funciones::get('numero');

try {
	$strFechas = Funciones::strFechas($desde, $hasta, 'fecha_documento') . ' AND ';
	$where = 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= (is_null($numero) ? '' : 'cod_deposito_bancario = ' . Datos::objectToDB($numero) . ' AND ');
	$where .= (is_null($strFechas) ? '' : $strFechas . ' AND ');
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha_documento DESC';

	$depositosBancarios = Factory::getInstance()->getListObject('DepositoBancarioCabecera', $where . $order);
	if (count($depositosBancarios) == 0)
		throw new FactoryExceptionCustomException('No hay depositos bancarios con ese filtro');

	$arr = array();
	foreach ($depositosBancarios as $depositoBancario) {
		$arr[] = jsonOrdenDePago($depositoBancario);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>