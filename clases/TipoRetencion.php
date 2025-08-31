<?php

/**
 * @property Imputacion		$imputacion
 */

class TipoRetencion extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	public		$idImputacion;
	protected	$_imputacion;

	//GETS y SETS
	protected function getImputacion() {
		if (!isset($this->_imputacion)){
			$this->_imputacion = Factory::getInstance()->getImputacion($this->idImputacion);
		}
		return $this->_imputacion;
	}
	protected function setImputacion($imputacion) {
		$this->_imputacion = $imputacion;
		return $this;
	}
}

?>