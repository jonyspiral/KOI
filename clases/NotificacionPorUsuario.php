<?php

/**
 * @property Notificacion	$notificacion
 */

class NotificacionPorUsuario extends Usuario {
	const		_primaryKey = '["id", "idNotificacion"]';

	public		$idNotificacion;
	protected	$_notificacion;
	//public		$anulado; //Ya está declarado en Usuario. Pero en el fill pongo el de NotifPorUsuario
	public		$vista;
	public		$eliminable;
	public		$fechaUltimaMod;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getNotificacion(){
		if (!isset($this->_notificacion)){
			$this->_notificacion = Factory::getInstance()->getNotificacion($this->idNotificacion);
		}
		return  $this->_notificacion;
	}

	protected function setNotificacion($notificacion){
		$this->_notificacion = $notificacion;
		return $this;
	}
}
?>