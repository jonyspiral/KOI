<?php

/**
 * @property Usuario		$usuario
 * @property Imputacion		$imputacion
 */

class Impuesto extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$tipo;
	public		$nombre;
	public		$descripcion;
	public		$idImputacion;
	protected	$_imputacion;
	public		$porcentaje;
	public		$esGravado;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function esGravado() {
		return $this->esGravado == 'S';
	}

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