<?php

/**
 * @property RutaProduccion		$rutaProduccion
 * @property SeccionProduccion	$seccionProduccion
 */

class RutaProduccionPaso extends Base {
	const		_primaryKey = '["idRutaProduccion", "nroPaso"]';

	public		$idRutaProduccion;
	protected	$_rutaProduccion;
	public		$nroPaso;
	public		$nroSubPaso;
	public		$anulado;
	public		$idSeccionProduccion;
	protected	$_seccionProduccion;
	public		$ejecucion;
	public		$duracion;
	public		$puntoProgramacion;
	public		$jerarquiaSeccion;
	public		$tieneSubordinadas;
	public		$imprimirOrdenF2;	//"N"
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	//GETS y SETS
	protected function getRutaProduccion() {
		if (!isset($this->_rutaProduccion)){
			$this->_rutaProduccion = Factory::getInstance()->getRutaProduccion($this->idRutaProduccion);
		}
		return $this->_rutaProduccion;
	}
	protected function setRutaProduccion($rutaProduccion) {
		$this->_rutaProduccion = $rutaProduccion;
		return $this;
	}
	protected function getSeccionProduccion() {
		if (!isset($this->_seccionProduccion)){
			$this->_seccionProduccion = Factory::getInstance()->getSeccionProduccion($this->idSeccionProduccion);
		}
		return $this->_seccionProduccion;
	}
	protected function setSeccionProduccion($seccionProduccion) {
		$this->_seccionProduccion = $seccionProduccion;
		return $this;
	}
}

?>