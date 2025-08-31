<?php

/**
 * @property RutaProduccionPaso[]	$pasos
 */

class RutaProduccion extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$nombre;
	protected	$_pasos;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	//GETS y SETS
	protected function getPasos() {
		if (!isset($this->_pasos) && isset($this->id)){
			$this->_pasos = Factory::getInstance()->getListObject('RutaProduccionPaso', 'cod_ruta = ' . Datos::objectToDB($this->id));
		}
		return $this->_pasos;
	}
	protected function setPasos($pasos) {
		$this->_pasos = $pasos;
		return $this;
	}
}

?>