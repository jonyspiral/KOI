<?php

/**
 * @property BancoPropio	$banco
 * @property CuentaBancaria	$cuentaBancaria
 * @property Array			$detalle
 */

class Chequera extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idCuentaBancaria;
	protected	$_cuentaBancaria;
	public		$fecha;
	public		$numeroInicio;
	public		$numeroFin;
	protected	$_detalle;
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
	protected function getDetalle() {
		if (!isset($this->_detalle) && isset($this->id)){
			$this->_detalle = Factory::getInstance()->getListObject('ChequeraItem', 'cod_chequera = ' . Datos::objectToDB($this->id) . ' AND utilizado = ' . Datos::objectToDB('N'));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
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
	protected function getCuentaBancaria() {
		if (!isset($this->_cuentaBancaria)){
			$this->_cuentaBancaria = Factory::getInstance()->getCuentaBancaria($this->idCuentaBancaria);
		}
		return $this->_cuentaBancaria;
	}
	protected function setCuentaBancaria($cuentaBancaria) {
		$this->_cuentaBancaria = $cuentaBancaria;
		return $this;
	}
}
