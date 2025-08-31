<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/alicuotas_retenciones/agregar/')) { ?>
<?php

$mes = Funciones::post('mes');
$ano = Funciones::post('ano');
$detalle = Funciones::post('detalle');
$clonarMesAnterior = Funciones::post('clonarMesAnterior') == 'S';

try {
	Factory::getInstance()->beginTransaction();

	if($clonarMesAnterior){
		RetencionTabla::clonarMesAnterior();
	}else {
		if(!checkdate($mes , 1, Funciones::hoy('Y'))){
			throw new FactoryExceptionCustomException('El mes es inconsistente');
		}
		if(!checkdate(1 , 1, $ano)){
			throw new FactoryExceptionCustomException('El año es inconsistente');
		}

		RetencionTabla::validarExistencia($mes, $ano);

		$i = 0;
		foreach($detalle as $item){
			if($item['inscriptoPorc'] > 100){
				throw new FactoryExceptionCustomException('Los porcentajes no pueden ser mayores a 100');
			}
			if($item['noInscriptoPorc'] > 100){
				throw new FactoryExceptionCustomException('Los porcentajes no pueden ser mayores a 100');
			}

			$i++;
			$retencionTabla = Factory::getInstance()->getRetencionTabla();

			$retencionTabla->mes = $mes;
			$retencionTabla->ano = $ano;
			$retencionTabla->item = $i;
			$retencionTabla->concepto = $item['concepto'];
			$retencionTabla->escalaDirecto = (empty($item['inscriptoPorc']) ? 'E' : 'D');
			$retencionTabla->baseImponible = $item['montoNosujeto'];
			$retencionTabla->inscriptoAlicuota = (empty($item['inscriptoPorc']) ? 'S/ESCALA' : Funciones::formatearDecimales($item['inscriptoPorc'], 2, '.'));
			$retencionTabla->noInscriptoAlicuota = Funciones::formatearDecimales($item['inscriptoPorc'], 2, '.');
			$retencionTabla->noCorrespondeMenor = $item['minRetencion'];

			$retencionTabla->guardar();
		}
	}

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('Las alicuotas fueron guardadas correctamente');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el impuesto');
}

?>
<?php } ?>