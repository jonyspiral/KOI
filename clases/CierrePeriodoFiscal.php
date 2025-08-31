<?php

/**
 * @property TipoPeriodoFiscal		$tipoPeriodoFiscal
 * @property Usuario				$usuario
 * @property Usuario				$usuarioBaja
 * @property Usuario				$usuarioUltimaMod
 */

class CierrePeriodoFiscal extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idTipoPeriodoFiscal;
	protected	$_tipoPeriodoFiscal;
	public		$fechaDesde;
	public		$fechaHasta;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	/**
	 * @param $fecha
	 * @param $tipoPeriodoFiscal TipoPeriodoFiscal
	 *
	 * @return bool
	 * @throws FactoryExceptionCustomException
	 */
	public static function comprobarFecha($fecha, $tipoPeriodoFiscal) {
		//Devuelve un TRUE o tira EXCEPTION
		$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
		$where .= 'cod_tipo_periodo = ' . Datos::objectToDB($tipoPeriodoFiscal->id) . ' AND ';
		$where .= 'dbo.toDate(' . Datos::objectToDB($fecha) . ') BETWEEN ';
		$where .= 'dbo.relativeDate(fecha_desde, ' . Datos::objectToDB('today') . ', 0) AND ';
		$where .= 'dbo.relativeDate(fecha_hasta, ' . Datos::objectToDB('today') . ', 0)';

		$cierres = Factory::getInstance()->getListObject('CierrePeriodoFiscal', $where);
		if (count($cierres)) {
			/** @var CierrePeriodoFiscal $cierre */
			$cierre = $cierres[0];
			throw new FactoryExceptionCustomException('La fecha fiscal indicada pertenece a un período fiscal "' . $cierre->tipoPeriodoFiscal->nombre . '" ya cerrado');
		}
		return true;
	}

	//GETS y SETS
	protected function getTipoPeriodoFiscal() {
		if (!isset($this->_tipoPeriodoFiscal)){
			$this->_tipoPeriodoFiscal = Factory::getInstance()->getTipoPeriodoFiscal($this->idTipoPeriodoFiscal);
		}
		return $this->_tipoPeriodoFiscal;
	}
	protected function setTipoPeriodoFiscal($tipoPeriodoFiscal) {
		$this->_tipoPeriodoFiscal = $tipoPeriodoFiscal;
		return $this;
	}
}

?>