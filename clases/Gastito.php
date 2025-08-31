<?php

/**
 * @property PersonaGasto		$personaGasto
 * @property Usuario			$usuario
 * @property Caja				$caja
 * @property RendicionGastos	$rendicionGastos
 */

class Gastito extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$empresa;
	public		$importe;
	public		$fecha;
	public		$idPersonaGasto;
	protected	$_personaGasto;
	public		$comprobante;
	public		$observaciones;
	public		$idCaja;
	protected	$_caja;
	public		$idRendicionGastos;
	protected	$_rendicionGastos;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaUltimaMod;

	public function consolidado() {
		return !is_null($this->idRendicionGastos);
	}

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
	protected function getPersonaGasto() {
		if (!isset($this->_personaGasto)){
			$this->_personaGasto = Factory::getInstance()->getPersonaGasto($this->idPersonaGasto);
		}
		return $this->_personaGasto;
	}
	protected function setPersonaGasto($personaGasto) {
		$this->_personaGasto = $personaGasto;
		return $this;
	}
	protected function getRendicionGastos() {
		if (!isset($this->_rendicionGastos)){
			$this->_rendicionGastos = Factory::getInstance()->getRendicionGastos($this->idRendicionGastos, $this->empresa);
		}
		return $this->_rendicionGastos;
	}
	protected function setRendicionGastos($rendicionGastos) {
		$this->_rendicionGastos = $rendicionGastos;
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