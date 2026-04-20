<?php

/**
 * @property AsientoContableModelo	$asientoContableModelo
 * @property Imputacion				$imputacion
 * @property Usuario				$usuario
 * @property Usuario				$usuarioBaja
 * @property Usuario				$usuarioUltimaMod
 */

class AsientoContableModeloFila extends Base {
	const		_primaryKey = '["idAsientoContableModelo", "numeroFila"]';

	public		$idAsientoContableModelo;
	protected	$_asientoContableModelo;
	public		$numeroFila;
	public		$idImputacion;
	protected	$_imputacion;
	public		$observaciones;
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

	//GETS y SETS
	protected function getAsientoContableModelo() {
		if (!isset($this->_asientoContableModelo)){
			$this->_asientoContableModelo = Factory::getInstance()->getAsientoContableModelo($this->idAsientoContableModelo);
		}
		return $this->_asientoContableModelo;
	}
	protected function setAsientoContableModelo($asientoContableModelo) {
		$this->_asientoContableModelo = $asientoContableModelo;
		return $this;
	}
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