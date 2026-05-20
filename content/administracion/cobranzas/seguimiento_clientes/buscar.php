<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/seguimiento_clientes/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');

try {
	if (empty($idCliente)) {
		throw new FactoryExceptionCustomException('Debe seleccionar un cliente');
	}

	$where = array();
	$strFechas = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha_gestion');
	if ($strFechas) {
		$where[] = $strFechas;
	}
	$where[] = '(anulado = ' . Datos::objectToDB('N') . ' OR anulado IS NULL)';
	$where[] = 'cod_cli = ' . Datos::objectToDB($idCliente);
	$orderBy = ' ORDER BY fecha_gestion DESC';

	$seguimientoCliente = Factory::getInstance()->getListObject('SeguimientoCliente', implode(' AND ', $where) . $orderBy);

	foreach($seguimientoCliente as $item) {
		/** @var SeguimientoCliente $item */
		$item->expand();
	}
	Html::jsonEncode('', $seguimientoCliente);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>
