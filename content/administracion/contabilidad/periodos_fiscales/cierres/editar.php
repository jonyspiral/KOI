<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/periodos_fiscales/cierres/editar/')) { ?>
<?php

$id = Funciones::post('id');
$idTipoPeriodo = Funciones::post('idTipoPeriodoFiscal');
$fechaDesde = Funciones::post('fechaDesde');
$fechaHasta = Funciones::post('fechaHasta');

try {
	$cierre = Factory::getInstance()->getCierrePeriodoFiscal($id);
	$cierre->tipoPeriodoFiscal = Factory::getInstance()->getTipoPeriodoFiscal($idTipoPeriodo);
	$cierre->fechaDesde = $fechaDesde;
	$cierre->fechaHasta = $fechaHasta;

	$cierre->guardar()->notificar('administracion/contabilidad/periodos_fiscales/cierres/editar/');

	Html::jsonSuccess('Se guardó correctamente el cierre de período fiscal', $cierre->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el cierre de período fiscal');
}

?>
<?php } ?>