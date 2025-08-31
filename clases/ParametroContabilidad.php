<?php

/**
 * @property Imputacion			$imputacion
 * @property Usuario			$usuario
 * @property Usuario			$usuarioBaja
 * @property Usuario			$usuarioUltimaMod
 */

class ParametroContabilidad extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
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