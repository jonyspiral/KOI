<?php

/**
 * @property AutorizacionTipo	$autorizacionTipo
 * @property Usuario			$usuario
 */

class AutorizacionPersona extends Base {
	const		_primaryKey = '["idAutorizacionTipo", "numero", "idUsuario"]';

	public		$idAutorizacionTipo;
	protected	$_autorizacionTipo;
	public		$numero;
	public		$idUsuario;
	protected	$_usuario;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getAutorizacionTipo() {
		if (!isset($this->_autorizacionTipo)){
			$this->_autorizacionTipo = Factory::getInstance()->getAutorizacionTipo($this->idAutorizacionTipo);
		}
		return $this->_autorizacionTipo;
	}
	protected function setAutorizacionTipo($autorizacionTipo) {
		$this->_autorizacionTipo = $autorizacionTipo;
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

?>