<?php

/**
 * @property RolPorTipoNotificacion[]		$roles
 * @property UsuarioPorTipoNotificacion[]	$usuarios
 */

class TipoNotificacion extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	public		$accionNotificacion;
	public		$accionAnular;
	public		$accionCumplido;
	public		$anularAlCumplir;
	public		$link;
	public		$detalle;
	public		$imagen;
	protected	$_roles;
	protected	$_usuarios;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function borrar() {
		foreach ($this->roles as $rol) {
			Factory::getInstance()->marcarParaBorrar($rol);
		}
		foreach ($this->usuarios as $usuario) {
			Factory::getInstance()->marcarParaBorrar($usuario);
		}
		return parent::borrar();
	}

	public function addUsuario(UsuarioPorTipoNotificacion $usuario) {
		$this->getUsuarios(); //En caso de nuevo, esto me va a traer un array vacío
		$this->_usuarios[] = $usuario;
	}

	public function addRol(RolPorTipoNotificacion $rol) {
		$this->getRoles(); //En caso de nuevo, esto me va a traer un array vacío
		$this->_roles[] = $rol;
	}

	//GETS y SETS
	protected function getRoles() {
		if (!isset($this->_roles) && isset($this->id)){
			$this->_roles = Factory::getInstance()->getListObject('RolPorTipoNotificacion', 'anulado = \'N\' AND cod_tipo_notificacion = ' . Datos::objectToDB($this->id));
		}
		return $this->_roles;
	}
	protected function setRoles($roles) {
		$this->_roles = $roles;
		return $this;
	}
	protected function getUsuarios() {
		if (!isset($this->_usuarios) && isset($this->id)){
			$this->_usuarios = Factory::getInstance()->getListObject('UsuarioPorTipoNotificacion', 'anulado = \'N\' AND cod_tipo_notificacion = ' . Datos::objectToDB($this->id));
		}
		return $this->_usuarios;
	}
	protected function setUsuarios($usuarios) {
		$this->_usuarios = $usuarios;
		return $this;
	}
}

?>