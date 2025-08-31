<?php

/**
 */

class RetencionTabla extends Base {
	public		$ano;
	public		$mes;
	public		$item;
	public		$concepto;
	public		$escalaDirecto;
	public		$baseImponible;
	public		$inscriptoAlicuota;
	public		$noInscriptoAlicuota;
	public		$noCorrespondeMenor;

	public static function clonarMesAnterior(){
		$mesActual = Funciones::hoy('n');
		$anoActual = Funciones::hoy('Y');

		self::validarExistencia($mesActual, $anoActual);

		$mesAnterior = date('m', strtotime(date('Y-m')." -1 month"));
		$anoAnterior = date('Y', strtotime(date('Y-m')." -1 month"));

		$retencionesTabla = Factory::getInstance()->getListObject('RetencionTabla', 'mes_num = ' . Datos::objectToDB($mesAnterior) . ' AND ano = ' . Datos::objectToDB($anoAnterior));

		foreach($retencionesTabla as $retencionTabla){
			/** @var RetencionTabla $nuevaRetencioneTabla */
			$nuevaRetencioneTabla = clone $retencionTabla;
			Factory::getInstance()->marcarParaInsertar($nuevaRetencioneTabla);
			$nuevaRetencioneTabla->mes = $mesActual;
			$nuevaRetencioneTabla->ano = $anoActual;

			$nuevaRetencioneTabla->guardar();
		}
	}

	public static function validarExistencia($mes = null, $anio = null){
		$mes = (is_null($mes) ? Funciones::hoy('n') : $mes);
		$anio = (is_null($anio) ? Funciones::hoy('Y') : $anio);

		$retencionesTabla = Factory::getInstance()->getListObject('RetencionTabla', 'mes_num = ' . Datos::objectToDB($mes) . ' AND ano = ' . Datos::objectToDB($anio));

		if(count($retencionesTabla) > 0){
			throw new FactoryExceptionCustomException('Ya se crearon las retenciones para el mes ' . $mes . ' año ' . $anio);
		}
	}

	//GETS Y SETS
}

?>