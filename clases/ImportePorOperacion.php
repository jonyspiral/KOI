<?php
/**
 * @property Caja				$caja
 * @property array				$detalle
 * @property TransferenciaBase	$operacion
 */
class ImportePorOperacion extends Base {
	public		$idImportePorOperacion;
	public 		$tipoOperacion;
	public		$idCaja;
	protected 	$_caja;
	public		$fechaCaja;
	public		$fechaAlta;
	protected	$_detalle;
	protected 	$_operacion;

	private		$efectivo;
	private		$cheques;
	private		$retencionesEfectuadas;
	private		$retencionesSufridas;
	private		$transferencias;

	//Este es un invento de Ariel para ver si la impresión en PDF puede ser mas feliz!
	public function getImportes() {
		$efectivo = array();
		$cheques = array();
		$retencionesEfectuadas = array();
		$retencionesSufridas = array();
		$transferencias = array();

		foreach($this->detalle as $item){
			/** ImportePorOperacionItem $item */
			$item->importe->getTipoImporte() == TiposImporte::efectivo && $efectivo[] = $item->importe;
			$item->importe->getTipoImporte() == TiposImporte::cheque && $cheques[] = $item->importe;
			$item->importe->getTipoImporte() == TiposImporte::retencionEfectuada && $retencionesEfectuadas[] = $item->importe;
			$item->importe->getTipoImporte() == TiposImporte::retencionSufrida && $retencionesSufridas[] = $item->importe;
			$item->importe->getTipoImporte() == TiposImporte::transferenciaBancariaImporte && $transferencias[] = $item->importe;
		};
		$this->efectivo = $efectivo;
		$this->cheques = $cheques;
		$this->retencionesEfectuadas = $retencionesEfectuadas;
		$this->retencionesSufridas = $retencionesSufridas;
		$this->transferencias = $transferencias;
	}

	public function getEfectivo() {
		if (!isset($this->efectivo))
			$this->getImportes();

		return $this->efectivo;
	}

	public function getCheques() {
		if (!isset($this->cheques))
			$this->getImportes();

		return $this->cheques;
	}

	public function getRetencionesEfectuadas() {
		if (!isset($this->retencionesEfectuadas))
			$this->getImportes();

		return $this->retencionesEfectuadas;
	}

	public function getRetencionesSufridas() {
		if (!isset($this->retencionesSufridas))
			$this->getImportes();

		return $this->retencionesSufridas;
	}

	public function getTransferencias() {
		if (!isset($this->transferencias))
			$this->getImportes();

		return $this->transferencias;
	}

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
	protected function getDetalle() {
		if (!isset($this->_detalle) && isset($this->idImportePorOperacion)){
			$this->_detalle = Factory::getInstance()->getListObject('ImportePorOperacionItem', 'cod_importe_operacion = ' . Datos::objectToDB($this->idImportePorOperacion));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	public function getNombreClaseDocumento(){
		if(isset($this->tipoOperacion)){
			$reflectionClass = new ReflectionClass('TiposTransferenciaBase');
			$constants = $reflectionClass->getConstants();
			foreach($constants as $key => $value){
				if($value == $this->tipoOperacion){
					return ucfirst($key);
				}
			}
		}
		return false;
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
