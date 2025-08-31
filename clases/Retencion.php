<?php

/**
 * @property TipoRetencion		$tipoRetencion
 * @property int				$numeroCertificado
 */

abstract class Retencion extends Importe {
	public		$idTipoRetencion;
	protected	$_tipoRetencion;
	public		$nombre;
	protected	$_numeroCertificado;
	public		$cuit;
	public		$fecha;
	public		$declarada;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function simularComoImporte(){
		$importeRetencion = array();
		$importeRetencion['id'] = $this->id;
		$importeRetencion['tipoRetencion'] = array();
		$importeRetencion['tipoRetencion']['id'] = $this->tipoRetencion->id;
		$importeRetencion['importe'] = $this->importe;
		return $importeRetencion;
	}

	public static function validar($obj) {
		parent::validar($obj);
		if (!isset($obj['importe']) || !isset($obj['nombre']) || !isset($obj['cuit']) || !isset($obj['fecha'])) {
			throw new FactoryExceptionCustomException('No se reconoce el formato de una retención');
		}
		if (!isset($obj['importe']) || !isset($obj['tipoRetencion']['id'])) {
			throw new FactoryExceptionCustomException('No se reconoce el formato de una retención');
		}
	}

	public static function validarExistencia(/** @noinspection PhpUnusedParameterInspection */ Caja $caja, $retenciones) {
		return true;
	}

	public function getImputacion() {
		return $this->tipoRetencion->idImputacion;
	}

	public function getObservacionContabilidad() {
		return 'Nº de certificado: ' . $this->numeroCertificado;
	}

	//GETS Y SETS
	protected function setNumeroCertificado($numeroCertificado) {
		$this->_numeroCertificado = $numeroCertificado;
		return $this;
	}
	protected function getTipoRetencion() {
		if (!isset($this->_tipoRetencion)){
			$this->_tipoRetencion = Factory::getInstance()->getTipoRetencion($this->idTipoRetencion);
		}
		return $this->_tipoRetencion;
	}
	protected function setTipoRetencion($tipoRetencion) {
		$this->_tipoRetencion = $tipoRetencion;
		return $this;
	}
}

?>