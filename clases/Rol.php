<?php

/**
 * @property array	$funcionalidades
 * @property array	$indicadores
 * @property array	$usuarios
 */

class Rol extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$nombre;
	public		$tipo; //Personal o Contacto
	public		$fechaAlta;
	public		$fechaBaja;
	protected	$_funcionalidades;
	protected	$_indicadores;
	protected	$_usuarios;

	public function __construct() {
		parent::__construct();
	}

	public function addFuncionalidad(FuncionalidadPorRol $funcionalidad) {
		$this->getFuncionalidades(); //En caso de nuevo, esto me va a traer un array vacío
		$this->_funcionalidades[] = $funcionalidad;
	}
	//GETS y SETS
	protected function getFuncionalidades() {
		if (!isset($this->_funcionalidades) && isset($this->id)){
			$this->_funcionalidades = Factory::getInstance()->getListObject('FuncionalidadPorRol', 'cod_rol = ' . Datos::objectToDB($this->id));
		}
		return $this->_funcionalidades;
	}
	protected function setFuncionalidades($funcionalidades) {
		$this->_funcionalidades = $funcionalidades;
		return $this;
	}
	protected function getIndicadores() {
		if (!isset($this->_indicadores)) {
			$ixr = Factory::getInstance()->getListObject('IndicadorPorRol', 'cod_rol = ' . Datos::objectToDB($this->id));
			$this->_indicadores = array();
			foreach($ixr as $aux) {
				$this->_indicadores[] = $aux->indicador;
			}
		}
		return $this->_indicadores;
	}
	protected function setIndicadores($indicadores) {
		$this->_indicadores = $indicadores;
		return $this;
	}
	protected function getUsuarios() {
		if (!isset($this->_usuarios) && isset($this->id)){
			$this->_usuarios = Factory::getInstance()->getListObject('RolPorUsuario', 'cod_rol = ' . Datos::objectToDB($this->id));
		}
		return $this->_usuarios;
	}
	protected function setUsuarios($usuarios) {
		$this->_usuarios = $usuarios;
		return $this;
	}
}

?>