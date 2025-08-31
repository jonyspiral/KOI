<?php

/**
 * @property int 	$id
 */

class Operador extends Personal {
	const		_primaryKey = '["id"]';

	protected	$_id;
	public		$tipo;
	public		$porcComisionVtas;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getId() {
		if (!isset($this->_id) && isset($this->idPersonal) && isset($this->tipo)){
			$this->_id = $this->tipo . Funciones::padLeft($this->idPersonal, 5, '0');
		}
		return $this->_id;
	}
	protected function setId($id) {
		$this->_id = $id;
		return $this;
	}
}

?>