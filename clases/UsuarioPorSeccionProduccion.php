<?php

/**
 * @property SeccionProduccion	$seccionProduccion
 */

class UsuarioPorSeccionProduccion extends Usuario {
	const		_primaryKey = '["id", "idSeccionProduccion"]';

	public		$idSeccionProduccion;
	protected	$_seccionProduccion;

	//GETS y SETS
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