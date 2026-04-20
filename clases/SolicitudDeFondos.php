<?php

/**
 * @property Caja  		$cajaSolicitado
 * @property Caja  		$cajaSolicitante
 * @property array  	$detalle
 */

class SolicitudDeFondos extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idCajaSolicitante;
	protected 	$_cajaSolicitante;
	public		$idCajaSolicitado;
	protected 	$_cajaSolicitado;
	public		$cerrada;
	public		$aprobada;
	protected 	$_detalle;

	//GETS y SETS
	protected function getCajaSolicitado() {
		if (!isset($this->_cajaSolicitado)){
			$this->_cajaSolicitado = Factory::getInstance()->getCaja($this->idCajaSolicitado);
		}
		return $this->_cajaSolicitado;
	}
	protected function setCajaSolicitado($cajaSolicitado) {
		$this->_cajaSolicitado = $cajaSolicitado;
		return $this;
	}
	protected function getCajaSolicitante() {
		if (!isset($this->_cajaSolicitante)){
			$this->_cajaSolicitante = Factory::getInstance()->getCaja($this->idCajaSolicitante);
		}
		return $this->_cajaSolicitante;
	}
	protected function setCajaSolicitante($cajaSolicitante) {
		$this->_cajaSolicitante = $cajaSolicitante;
		return $this;
	}
	protected function getDetalle() {
		if (!isset($this->_detalle) && isset($this->id)){
			$this->_detalle = Factory::getInstance()->getListObject('SolicitudDeFondosItem', 'cod_solicitud_de_fondos = ' . Datos::objectToDB($this->id));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
}

?>