<?php

/**
 * @property Usuario		$usuario
 */

class Concepto extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	public		$descripcion;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	//GETS y SETS
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

?>