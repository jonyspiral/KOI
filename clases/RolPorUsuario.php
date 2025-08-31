<?php

/**
 * @property Usuario	$usuario
 */

class RolPorUsuario extends Rol {
	const		_primaryKey = '["idUsuario", "id"]';

	public		$idUsuario;
	protected	$_usuario;

	public function __construct() {
		parent::__construct();
	}

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