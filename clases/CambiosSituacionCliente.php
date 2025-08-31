<?php

/**
 * @property Cliente				$cliente
 * @property Usuario				$usuario
 */

class CambiosSituacionCliente extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idCliente;
	protected	$_cliente;
	public		$calificacionNueva;
	public		$calificacionAnterior;
	public		$idUsuario;
	protected	$_usuario;
	public		$fecha;
	public		$hora;

	//GETS y SETS
	protected function getCliente() {
		if (!isset($this->_cliente)){
			$this->_cliente = Factory::getInstance()->getClienteTodos($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
		return $this;
	}
}

?>