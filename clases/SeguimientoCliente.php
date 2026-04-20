<?php

/**
 * @property Cliente			$cliente
 * @property Usuario			$usuario
 */
class SeguimientoCliente extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idCliente;
	protected	$_cliente;
	public		$fechaGestion;
	public		$observaciones;
	public		$estado;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$fechaAlta;
	public		$fechaUltimaMod;
	public		$fechaBaja;

	//GETS y SETS
	protected function getCliente() {
		if (!isset($this->_cliente)){
			$this->_cliente = Factory::getInstance()->getCliente($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
		return $this;
	}
}

?>