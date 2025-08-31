<?php

/**
 * @property Banco	$banco
 * @property Caja	$caja
 */

class BancoPropio extends Base {
	const		_primaryKey = '["idBanco", "idSucursal"]';

	public		$idBanco;
	protected	$_banco;
	public		$idSucursal;
	protected	$_nombre;
	public		$nombreSucursal;
	protected	$_cuentas;
	public		$direccion;
	public		$imputacionContable;
	public		$fechaInicioCuenta;
	public		$telefono;
	public		$observaciones;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function __construct() {
		parent::__construct();
		$this->direccion = new Direccion();
	}

	//GETS y SETS
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
	protected function getCuentas() {
		if (!isset($this->_cuentas)){
			$where = 'cod_banco = ' . Datos::objectToDB($this->idBanco) . ' AND cod_sucursal_banco = ' . Datos::objectToDB($this->idSucursal);
			$this->_cuentas = Factory::getInstance()->getListObject('CuentaBancaria', $where);
		}
		return $this->_cuentas;
	}
	protected function setCuentas($cuentas) {
		$this->_cuentas = $cuentas;
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
}

?>