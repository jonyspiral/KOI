<?php

/**
 * @deprecated NO USAR. Se usa PermisoPorUsuarioPorCaja
 * @property Caja		$caja
 * @property Usuario	$usuario
 * @property array		$permisos
 * @property bool		$esResponsable
 */

class UsuarioPorCaja extends Usuario{
	const		_primaryKey = '["idCaja", "idUsuario"]';

	public		$idCaja;
	protected	$_caja;
	public		$idUsuario;
	protected	$_usuario;
	protected	$_permisos;
	protected	$_esResponsable;

	public function puede($permiso) {
		foreach($this->getPermisos() as $permisoCaja){
			if($permisoCaja->idPermiso == $permiso)
				return true;
		}
		return false;
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
	protected function getEsResponsable() {
		if (!isset($this->_esResponsable)){
			$this->_esResponsable = ($this->getCaja()->idResponsable == $this->idUsuario);
		}
		return $this->_esResponsable;
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
	protected function getPermisos() {
		if (!isset($this->_permisos)){
			$this->_permisos = array();
			$this->_permisos = Factory::getInstance()->getListObject('PermisoPorUsuarioPorCaja', 'cod_usuario = '
								. Datos::objectToDB($this->idUsuario) . ' AND cod_caja = ' . Datos::objectToDB($this->idCaja));
		}
		return $this->_permisos;
	}
}
