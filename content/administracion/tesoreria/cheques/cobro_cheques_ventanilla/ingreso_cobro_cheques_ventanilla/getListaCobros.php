<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/cobro_cheques_ventanilla/ingreso_cobro_cheques_ventanilla/buscar/')) { ?>
<?php

function expand(CobroChequeVentanillaTemporal $cobro) {
	$arr = array();
	$arr['id'] = $cobro->id;
	$arr['nombreCaja'] = $cobro->caja->nombre;
	$arr['nombreResponsable'] = $cobro->responsable->nombreApellido;
	$arr['cantCheques'] = count($cobro->cheques);
	$arr['importeTotal'] = $cobro->importeTotal;

	return $arr;
}

try {
	$order = ' ORDER BY fecha_alta';
	$where = 'confirmado = ' . Datos::objectToDB('N');
	$where .= ' AND anulado = ' . Datos::objectToDB('N');

	$arr = array();
	$cobros = Factory::getInstance()->getListObject('CobroChequeVentanillaTemporal', $where . $order);
	foreach ($cobros as $cobro) {
		$arr[] = expand($cobro);
	}

	Html::jsonEncode('', $arr);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>