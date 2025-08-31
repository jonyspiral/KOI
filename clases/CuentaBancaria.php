<?php

/**
 * @property Banco			$banco
 * @property BancoPropio	$sucursal
 * @property string			$nombre
 * @property Caja			$caja
 * @property Proveedor		$proveedor
 * @property Imputacion		$imputacion
 */

class CuentaBancaria extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idBanco;
	protected	$_banco;
	public		$idSucursal;
	protected	$_sucursal;
	protected	$_nombre;
	public		$idCaja;
	protected	$_caja;
	public		$idProveedor;
	protected	$_proveedor;
	public		$idImputacion;
	protected	$_imputacion;
	public		$numeroCuenta;
	public		$nombreCuenta;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

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
	protected function getBanco() {
		if (!isset($this->_banco)){
			$this->_banco = Factory::getInstance()->getBanco($this->idBanco);
		}
		return $this->_banco;
	}
	protected function setBanco($banco) {
		$this->_banco = $banco;
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
	protected function getNombre() {
		if (!isset($this->_nombre)){
			$this->_nombre = $this->banco->nombre;
		}
		return $this->_nombre;
	}
	protected function setNombre($nombre) {
		$this->_nombre = $nombre;
		return $this;
	}
	protected function getProveedor() {
		if (!isset($this->_proveedor)){
			$this->_proveedor = Factory::getInstance()->getProveedor($this->idProveedor);
		}
		return $this->_proveedor;
	}
	protected function setProveedor($proveedor) {
		$this->_proveedor = $proveedor;
		return $this;
	}
	protected function getSucursal() {
		if (!isset($this->_sucursal)){
			$this->_sucursal = Factory::getInstance()->getBancoPropio($this->idBanco, $this->idSucursal);
		}
		return $this->_sucursal;
	}
	protected function setSucursal($sucursal) {
		$this->_sucursal = $sucursal;
		return $this;
	}
}

?>