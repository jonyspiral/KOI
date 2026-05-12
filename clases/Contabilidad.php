<?php

abstract class Contabilidad {
	public static function contabilizarDocumento(DocumentoContable $documento) {
		/** @var Base $documento */
		if ($documento->modo == Modos::delete) {
			return self::descontabilizarDocumento($documento);
		}
		$empresa = $documento->contabilidadEmpresa();
		$nombre = $documento->contabilidadNombre();
		$fecha = $documento->contabilidadFecha();
		$detalleJson = $documento->contabilidadDetalle();
		$idAsiento = $documento->contabilidadIdAsientoContable();

		return self::contabilizar($empresa, $nombre, $fecha, $detalleJson, $idAsiento);
	}

	public static function contabilizar($empresa, $nombre, $fecha, $detalleJson, $idAsiento = null) {
		$ejercicioContable = self::getEjercicioContable($fecha);
		$asiento = Factory::getInstance()->getAsientoContable($idAsiento);

		return self::registrarAsiento($asiento, $empresa, $ejercicioContable, $nombre, $fecha, $detalleJson);
	}

	private static function registrarAsiento(AsientoContable $asiento, $empresa, $ejercicioContable, $nombre, $fecha, $detalleJson) {
		if (!$asiento->id) {
			$asiento->empresa = $empresa;
		}
		$asiento->ejercicioContable = $ejercicioContable;
		$asiento->nombre = $nombre;
		$asiento->fecha = $fecha;
		$asiento->loadDetalleJson($detalleJson);
		$asiento->guardar();

		return $asiento;
	}

	public static function descontabilizarDocumento(DocumentoContable $documento) {
		$idAsiento = $documento->contabilidadIdAsientoContable();
		return self::descontabilizar($idAsiento);
	}

	public static function descontabilizar($idAsiento) {
		$asiento = Factory::getInstance()->getAsientoContable($idAsiento);
		$asiento->borrar();
		return $asiento;
	}

	private static function getEjercicioContable($fecha) {
		$where = 'anulado = ' . Datos::objectToDB('N') . ' AND fecha_desde <= dbo.toDate(' . Datos::objectToDB($fecha) . ') AND fecha_hasta >= dbo.toDate(' . Datos::objectToDB($fecha) . ')';
		$ejercicios = Factory::getInstance()->getListObject('EjercicioContable', $where);
		if (count($ejercicios) != 1) {
			throw new FactoryExceptionCustomException('Ocurrió un error al intentar obtener el ejercicio contable correspondiente a la fecha "' . $fecha . '". Por favor revise el ABM de ejercicios contables');
		}
		return $ejercicios[0];
	}

	public static function getFechaBusquedaReporte(&$fechaAsientoDesde, $fechaAsientoHasta, $fechaVtoDesde, $fechaVtoHasta, &$warning) {
		$fechaAsiento = Funciones::strFechas($fechaAsientoDesde, $fechaAsientoHasta, 'fecha_asiento');
		$fechaVto = Funciones::strFechas($fechaVtoDesde, $fechaVtoHasta, 'fecha_vencimiento');

		if (!empty($fechaAsiento) && empty($fechaVto)){
			$fechaDesde = $fechaAsientoDesde;
			$fechaHasta = $fechaAsientoHasta;
			$campoFecha = 'fecha_asiento';
		} elseif (empty($fechaAsiento) && !empty($fechaVto)) {
			$fechaDesde = $fechaVtoDesde;
			$fechaHasta = $fechaVtoHasta;
			$campoFecha = 'fecha_vencimiento';
		} elseif (empty($fechaAsiento) && empty($fechaVto)) {
			$campoFecha = 'fecha_asiento';
		} else {
			throw new FactoryExceptionCustomException('No puede mezclar filtros de fechas de asientos y vencimiento');
		}

		if (is_null($fechaDesde) && is_null($fechaHasta)){
			$where = Datos::objectToDB(Funciones::hoy()) . 'BETWEEN fecha_desde AND fecha_hasta';
			$where .= ' AND anulado = ' . Datos::objectToDB('N');

			$ejerciciosContables = Factory::getInstance()->getListObject('EjercicioContable', $where);

			if (count($ejerciciosContables) == 0){
				throw new FactoryExceptionCustomException('No existe un ejercicio contable para el período actual');
			}
			if (count($ejerciciosContables) > 1){
				throw new FactoryExceptionCustomException('Existen ejercicios contables conflictivos');
			}

			$ejercicioContable = $ejerciciosContables[0];

			return Funciones::strFechas($ejercicioContable->fechaDesde, $ejercicioContable->fechaHasta, $campoFecha);
		} elseif ((is_null($fechaDesde) && !is_null($fechaHasta)) || (!is_null($fechaDesde) && is_null($fechaHasta))){
			if (is_null($fechaDesde)){
				$fecha = $fechaHasta;
			} else {
				$fecha = $fechaDesde;
			}
			$where = Datos::objectToDB($fecha) . 'BETWEEN fecha_desde AND fecha_hasta';
			$where .= ' AND anulado = ' . Datos::objectToDB('N');

			$ejerciciosContables = Factory::getInstance()->getListObject('EjercicioContable', $where);

			if (count($ejerciciosContables) == 0){
				throw new FactoryExceptionCustomException('No existe un ejercicio contable para el rango de fechas solicitado');
			}
			if (count($ejerciciosContables) > 1){
				throw new FactoryExceptionCustomException('Existen ejercicios contables conflictivos');
			}

			$ejercicioContable = $ejerciciosContables[0];

			if (is_null($fechaDesde)){
				return Funciones::strFechas($ejercicioContable->fechaDesde, $fechaHasta, $campoFecha);
			} else {
				return Funciones::strFechas($fechaDesde, $ejercicioContable->fechaHasta, $campoFecha);
			}
		} else {
			$where = '(' . Datos::objectToDB($fechaDesde) . 'BETWEEN fecha_desde AND fecha_hasta';
			$where .= ' OR ' . Datos::objectToDB($fechaHasta) . 'BETWEEN fecha_desde AND fecha_hasta)';
			$where .= ' AND anulado = ' . Datos::objectToDB('N');

			$ejerciciosContables = Factory::getInstance()->getListObject('EjercicioContable', $where);

			if (count($ejerciciosContables) == 0){
				throw new FactoryExceptionCustomException('No existe un ejercicio contable para el rango de fechas solicitado');
			}
			if (count($ejerciciosContables) > 1){
				$warning = true;
			}

			$fechaAsientoDesde = $fechaDesde;
			return Funciones::strFechas($fechaDesde, $fechaHasta, $campoFecha);
		}
	}
}

?>