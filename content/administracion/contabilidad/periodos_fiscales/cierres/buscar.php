<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/periodos_fiscales/cierres/buscar/')) { ?>
<?php

$idTipoPeriodoFiscal = Funciones::get('idTipoPeriodoFiscal');

try {
	$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
	$where .= 'cod_tipo_periodo = ' . Datos::objectToDB($idTipoPeriodoFiscal);
	$order = ' ORDER BY cod_cierre_periodo DESC';

	$cierres = Factory::getInstance()->getListObject('CierrePeriodoFiscal', $where . $order);
	foreach ($cierres as $cierre) {
		$cierre->expand();
	}

	Html::jsonEncode('', $cierres);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>