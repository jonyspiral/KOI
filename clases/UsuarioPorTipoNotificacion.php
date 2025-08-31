<?php

/**
 * @property TipoNotificacion	$tipoNotificacion
 */

class UsuarioPorTipoNotificacion extends Usuario {
	const		_primaryKey = '["idTipoNotificacion", "id"]';

	public		$eliminable;
	public		$idTipoNotificacion;
	protected	$_tipoNotificacion;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getTipoNotificacion() {
		if (!isset($this->_tipoNotificacion)){
			$this->_tipoNotificacion = Factory::getInstance()->getTipoNotificacion($this->idTipoNotificacion);
		}
		return $this->_tipoNotificacion;
	}
	protected function setTipoNotificacion($tipoNotificacion) {
		$this->_tipoNotificacion = $tipoNotificacion;
		return $this;
	}
}
?>