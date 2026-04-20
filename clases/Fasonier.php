<?php

/**
 * @property int 	$id
 */

class Fasonier extends Proveedor {
	const		_primaryKey = '["id"]';

	protected	$_id;
	public		$tipoOperador;

	public function __construct() {
		parent::__construct();
		$this->tipo = TiposOperador::fasonier;
	}

	//GETS y SETS
	protected function getId() {
		if (!isset($this->_id) && isset($this->id) && isset($this->tipoOperador)){
			$this->_id = $this->tipoOperador . Funciones::padLeft($this->id, 5, '0');
		}
		return $this->_id;
	}
	protected function setId($id) {
		$this->_id = $id;
		return $this;
	}
}

?>