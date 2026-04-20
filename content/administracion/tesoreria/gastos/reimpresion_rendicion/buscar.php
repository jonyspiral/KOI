<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/reimpresion_rendicion/buscar/')) { ?>
<?php

function jsonCheque(RendicionGastos $rendicionGastos) {
	$json = array();
	$json['numero'] = $rendicionGastos->numero;
	$json['empresa'] = $rendicionGastos->empresa;
	$json['importePendiente'] = $rendicionGastos->importePendiente;
	$json['importeTotal'] = $rendicionGastos->importeTotal;
	$json['fecha'] = $rendicionGastos->fecha;
	$json['observaciones'] = $rendicionGastos->observaciones;
	return $json;
}

$fechaDesde = Funciones::get('desde');
$fechaHasta = Funciones::get('hasta');
$empresa = Funciones::session('empresa');
$numero = Funciones::get('numero');
$arr = array();

try {
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_documento') . ' AND ';
	$where .= 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= (empty($numero) ? '' : ('cod_rendicion_gastos = ' . Datos::objectToDB($numero)) . ' AND ');
	$where = trim($where, ' AND ');
	$where = ($where ? $where : '1 = 1');
	$order = ' ORDER BY fecha_documento DESC';

	$rendicionesGastos = Factory::getInstance()->getListObject('RendicionGastos', $where . $order);
	if (count($rendicionesGastos) == 0) {
		throw new FactoryExceptionCustomException('No hay rendiciones con ese filtro');
	}

	$arr = array();
	foreach ($rendicionesGastos as $rendicionGastos) {
		$arr[] = jsonCheque($rendicionGastos);
	}
	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>