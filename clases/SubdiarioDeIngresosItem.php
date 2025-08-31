<?php
/**
 * @property Recibo		$recibo
 */
class SubdiarioDeIngresosItem extends Base {

	public		$numeroRecibo;
	public		$empresa;
	protected 	$_recibo;
	public		$cliente;
	public		$imputacion;
	public		$efectivo;
	public		$cheques;
	public		$transferencias;
	public		$total;
	public		$idCaja;
	public		$fecha;

	public function getArrayRetenciones() {
		return $this->recibo->getArrayRetenciones();
	}

	//GETS y SETS
	protected function getRecibo() {
		if (!isset($this->_recibo)){
			$this->_recibo = Factory::getInstance()->getRecibo($this->numeroRecibo, $this->empresa);
		}
		return $this->_recibo;
	}
	protected function setRecibo($recibo) {
		$this->_recibo = $recibo;
		return $this;
	}
}

?>