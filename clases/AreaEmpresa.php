<?php

/**
 * @property array				$usuarios
 * @property Usuario			$usuario
 * @property Usuario			$usuarioBaja
 * @property Usuario			$usuarioUltimaMod
 */

class AreaEmpresa extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	public		$habilitadaTicket;
	protected	$_usuarios;
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

	public function borrar() {
		foreach ($this->usuarios as $usuario) {
			Factory::getInstance()->marcarParaBorrar($usuario);
		}
		return parent::borrar();
	}

	public function habilitadaTicket() {
		return $this->habilitadaTicket == 'S';
	}

	//GETS y SETS
	protected function getUsuarios() {
		if (!isset($this->_usuarios) && isset($this->id)){
			$this->_usuarios = Factory::getInstance()->getListObject('UsuarioPorAreaEmpresa', 'id_area_empresa = ' . Datos::objectToDB($this->id));
		}
		return $this->_usuarios;
	}
	protected function setUsuarios($usuarios) {
		$this->_usuarios = $usuarios;
		return $this;
	}
}

?>