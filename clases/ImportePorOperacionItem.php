<?php

/**
 * @property Importe				$importe
 * @property ImportePorOperacion	$importePorOperacion
 * @property Caja					$caja
 * @property TransferenciaBase		$operacion
 */

class ImportePorOperacionItem extends Base {
	const		_primaryKey = '["idImporteOperacion", "tipoImporte", "idImporte"]';

	public		$idImportePorOperacion;
	protected 	$_importePorOperacion;
	public 		$tipoOperacion;
	public		$idCaja;
	protected 	$_caja;
	public 		$tipoImporte;
	public		$idImporte;
	protected 	$_importe;
	public		$anulado;
	protected 	$_operacion;

	//GETS y SETS
	protected function getCaja() {
		if (!isset($this->_caja)){
			$this->_caja = Factory::getInstance()->getCaja($this->idCaja);
		}
		return $this->_caja;
	}
	protected function setCaja($caja) {
		$this->_caja = $caja;
		return $this;
	}
	protected function getImporte() {
		if (!isset($this->_importe)){
			switch ($this->tipoImporte) {
				case TiposImporte::efectivo:
					$this->_importe = Factory::getInstance()->getEfectivo($this->idImporte);
					break;
				case TiposImporte::cheque:
					$this->_importe = Factory::getInstance()->getCheque($this->idImporte);
					break;
				case TiposImporte::transferenciaBancariaImporte:
					$this->_importe = Factory::getInstance()->getTransferenciaBancariaImporte($this->idImporte);
					break;
				case TiposImporte::retencionEfectuada:
					$this->_importe = Factory::getInstance()->getRetencionEfectuada($this->idImporte);
					break;
				case TiposImporte::retencionSufrida:
					$this->_importe = Factory::getInstance()->getRetencionSufrida($this->idImporte);
					break;
			}
		}
		return $this->_importe;
	}
	protected function setImporte($importe) {
		$this->_importe = $importe;
		return $this;
	}
	protected function getImportePorOperacion() {
		if (!isset($this->_importePorOperacion)){
			$this->_importePorOperacion = Factory::getInstance()->getImportePorOperacion($this->idImportePorOperacion);
		}
		return $this->_importePorOperacion;
	}
	protected function setImportePorOperacion($importePorOperacion) {
		$this->_importePorOperacion = $importePorOperacion;
		return $this;
	}
	protected function getOperacion() {
		if (!isset($this->_operacion)){
			$this->_operacion = TransferenciaBase::getFromImportePorOperacion($this->tipoOperacion, $this->idImportePorOperacion);
		}
		return $this->_operacion;
	}
	protected function setOperacion($operacion) {
		$this->_operacion = $operacion;
		return $this;
	}
}
