<?php

/**
 * @property Direccion			$direccion
 * @property CondicionIva		$condicionIva
 * @property Imputacion			$imputacion
 * */

class DocumentoGastoDatos extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$razonSocial;
	public		$idCondicionIva;
	protected	$_condicionIva;
	public		$idImputacion;
	protected	$_imputacion;
	public		$cuit;
	public		$direccion;
	public		$fechaAlta;

	public function __construct(){
		$this->direccion = new Direccion();
		parent::__construct();
	}

	//GETS y SETS
	protected function getCondicionIva() {
		if (!isset($this->_condicionIva)){
			$this->_condicionIva = Factory::getInstance()->getCondicionIva($this->idCondicionIva);
		}
		return $this->_condicionIva;
	}
	protected function setCondicionIva($condicionIva) {
		$this->_condicionIva = $condicionIva;
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