<?php

/**
 * @deprecated NO USAR. Se usa PermisoPorUsuarioPorCaja
 * @property Caja	$caja
 */

class PermisoPorCaja extends Base {
	const		_primaryKey = '["idCaja", "idUsuario"]';

	public		$idCaja;
	protected 	$_caja;
	public		$idPermiso;

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

}
