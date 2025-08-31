<?php

/**
 * @property Usuario     		$usuario
 */

class SolicitudDeFondosItem extends Base {
	const		_primaryKey = '["id", "orden"]';

	public		$id;
	public		$orden;
	public		$idUsuario;
	protected	$_usuario;
	public		$importe;
	public		$fechaSugerida;
	public		$motivo;
	public		$observaciones;

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