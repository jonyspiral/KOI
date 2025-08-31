<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/alicuotas_retenciones/buscar/')) { ?>
	<?php

$mes = Funciones::get('mes');
$ano = Funciones::get('ano');

try {
	$retencionesTabla = Factory::getInstance()->getListObject('RetencionTabla', 'mes_num = ' . Datos::objectToDB($mes) . ' AND ano = ' . Datos::objectToDB($ano));

	if(count($retencionesTabla) == 0){
		throw new FactoryExceptionCustomException('No existen alicuotas de retención para el mes y año seleccionados');
	}

	$return = array();
	$detalles = array();
	foreach($retencionesTabla as $retencionTabla){
		/** @var RetencionTabla $retencionTabla */
		$item = array();
		$item['item'] = $retencionTabla->item;
		$item['concepto'] = $retencionTabla->concepto;
		$item['montoNosujeto'] = $retencionTabla->baseImponible;
		$item['inscriptoPorc'] = $retencionTabla->inscriptoAlicuota;
		$item['noInscriptoPorc'] = $retencionTabla->noInscriptoAlicuota;
		$item['minRetencion'] = $retencionTabla->noCorrespondeMenor;

		$detalles[] = $item;
	}

	$return['detalle'] = $detalles;
	$return['ano'] = $ano;
	$return['mes'] = $mes;

	Html::jsonEncode('', $return);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>