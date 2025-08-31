<?php

/**
 * @property Caja		$caja
 * @property Usuario	$usuario
 */

class PermisoPorUsuarioPorCaja extends Base {
	const		_primaryKey = '["idCaja", "idUsuario", "idPermiso"]';

	public		$idCaja;
	protected 	$_caja;
	public		$idUsuario;
	protected 	$_usuario;
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
	protected function getUsuario() {
		if (!isset($this->_usuario)){
			$this->_usuario = Factory::getInstance()->getUsuario($this->idUsuario);
		}
		return $this->_usuario;
	}
	protected function setUsuario($usuario) {
		$this->_usuario = $usuario;
		return $this;
	}

}
