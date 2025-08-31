<?php

/**
 * @property string		$nombre
 * @property Rol		$rol
 */

class FuncionalidadPorRol extends Base {
	const		_primaryKey = '["idRol", "idFuncionalidad"]';

	public		$idFuncionalidad;
	protected	$_nombre;
	public		$idRol;
	protected	$_rol;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getNombre() {
		if (!isset($this->_nombre)){
			$this->_nombre = Funcionalidades::get($this->idFuncionalidad);
		}
		return $this->_nombre;
	}
	protected function getRol() {
		if (!isset($this->_rol)){
			$this->_rol = Factory::getInstance()->getRol($this->idRol);
		}
		return $this->_rol;
	}
	protected function setRol($rol) {
		$this->_rol = $rol;
		return $this;
	}
}

?>